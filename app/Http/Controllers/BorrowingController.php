<?php

namespace App\Http\Controllers;

use App\Models\Borrowing;
use App\Models\BorrowingNote;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BorrowingController extends Controller
{
    private const STATUSES = ['pending', 'approved', 'rejected', 'borrowed', 'returned', 'overdue'];
    private const INVENTORY_HOLD_STATUSES = ['approved', 'borrowed', 'overdue'];

    public function index(Request $request)
    {
        $query = Borrowing::query()->latest();

        if (! $this->isAdmin($request)) {
            $borrower = $this->borrowerFromSession($request);
            $query->where('borrower_nim', $borrower['nim']);
        }

        $borrowings = $query->paginate(10);

        return view('borrowing.index', [
            'borrowings' => $borrowings,
            'statuses' => $this->statusOptions(),
            'isAdmin' => $this->isAdmin($request),
        ]);
    }

    public function create(Request $request)
    {
        $item = $request->filled('item_id')
            ? Item::findOrFail($request->integer('item_id'))
            : null;

        return view('borrowing.request', [
            'item' => $item,
            'itemName' => $item?->name ?? $request->query('item_name'),
            'borrower' => $this->borrowerFromSession($request),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_id' => ['nullable', 'integer', 'exists:items,id'],
            'item_name' => ['required_without:item_id', 'string', 'max:255'],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'purpose' => ['required', 'string', 'min:10', 'max:1000'],
            'terms_accepted' => ['accepted'],
        ], [
            'item_name.required_without' => 'Nama barang wajib tersedia dari katalog.',
            'start_date.after_or_equal' => 'Tanggal mulai tidak boleh sebelum hari ini.',
            'end_date.after_or_equal' => 'Tanggal dan waktu pengembalian harus sama atau setelah tanggal mulai.',
            'purpose.min' => 'Keperluan peminjaman minimal 10 karakter.',
            'terms_accepted.accepted' => 'Anda harus menyetujui syarat dan ketentuan peminjaman.',
        ]);

        $startAt = Carbon::parse($validated['start_date']);
        $endAt = Carbon::parse($validated['end_date']);

        if ($endAt->lt($startAt)) {
            return back()
                ->withErrors(['end_date' => 'Tanggal dan waktu pengembalian harus sama atau setelah tanggal mulai.'])
                ->withInput();
        }

        $item = $request->filled('item_id')
            ? Item::findOrFail($validated['item_id'])
            : null;

        if ($item && ! $this->isAvailable($item)) {
            return back()
                ->withErrors(['item_name' => 'Barang yang dipilih sedang tidak tersedia untuk dipinjam.'])
                ->withInput();
        }

        $borrower = $this->borrowerFromSession($request);
        $itemName = $item?->name ?? $validated['item_name'];
        $borrowing = Borrowing::create([
            'item_id' => $item?->id,
            'item_name' => $itemName,
            'borrower_name' => $borrower['name'],
            'borrower_nim' => $borrower['nim'],
            'start_date' => $startAt->toDateString(),
            'start_datetime' => $startAt,
            'end_date' => $endAt->toDateString(),
            'end_datetime' => $endAt,
            'purpose' => $validated['purpose'],
            'status' => 'pending',
        ]);
        $message = "Halo Sherizka, saya {$borrower['name']} NIM {$borrower['nim']} ingin konfirmasi peminjaman {$itemName}";

        return redirect()
            ->route('borrowing.request', array_filter([
                'item_id' => $item?->id,
                'item_name' => $item ? null : $itemName,
            ]))
            ->with('borrowing_success', [
                'name' => $borrower['name'],
                'nim' => $borrower['nim'],
                'item' => $itemName,
                'borrowing_id' => $borrowing->id,
                'whatsapp_url' => $this->whatsappUrl($message),
            ]);
    }

    public function show(Request $request, Borrowing $borrowing)
    {
        $borrowing->load(['notes.user']);

        if (! $this->isAdmin($request)) {
            $borrower = $this->borrowerFromSession($request);

            abort_unless($borrowing->borrower_nim === $borrower['nim'], 403);
        }

        return view('borrowing.show', [
            'borrowing' => $borrowing,
            'statusLabel' => $this->statusOptions()[$borrowing->status] ?? 'Menunggu',
            'isAdmin' => $this->isAdmin($request),
            'fineAmount' => $this->calculateFine($borrowing),
        ]);
    }

    public function updateStatus(Request $request, Borrowing $borrowing)
    {
        abort_unless($this->isAdmin($request), 403);

        $validated = $request->validate([
            'status' => ['required', 'string', 'in:' . implode(',', self::STATUSES)],
            'admin_note' => ['nullable', 'string', 'max:1000'],
        ], [
            'status.in' => 'Status peminjaman tidak valid.',
            'admin_note.max' => 'Catatan admin maksimal 1000 karakter.',
        ]);

        DB::transaction(function () use ($borrowing, $validated) {
            $oldStatus = $borrowing->status;
            $newStatus = $validated['status'];

            $this->syncInventoryForStatusChange($borrowing, $oldStatus, $newStatus);

            $borrowing->update([
                'status' => $newStatus,
                'admin_note' => $validated['admin_note'] ?? $borrowing->admin_note,
            ]);
        });

        return back()->with('borrowing_status_updated', 'Status peminjaman berhasil diperbarui.');
    }

    // F-10: Pencatatan Penyerahan
    public function handoverForm(Borrowing $borrowing)
    {
        abort_unless(in_array($borrowing->status, ['approved', 'overdue']), 404, 'Peminjaman belum disetujui.');

        return view('borrowing.handover', [
            'borrowing' => $borrowing,
            'conditions' => $this->conditionOptions(),
        ]);
    }

    public function recordHandover(Request $request, Borrowing $borrowing)
    {
        abort_unless($this->isAdmin($request), 403);
        abort_unless(in_array($borrowing->status, ['approved', 'overdue']), 400, 'Status peminjaman tidak valid untuk penyerahan.');

        $validated = $request->validate([
            'handover_date' => ['required', 'date', 'after_or_equal:today'],
            'handover_condition' => ['required', 'string', 'in:' . implode(',', ['good', 'fair', 'damaged'])],
            'handover_photo' => ['nullable', 'image', 'max:2048'],
        ], [
            'handover_date.required' => 'Tanggal penyerahan wajib diisi.',
            'handover_condition.required' => 'Kondisi barang saat diserahkan wajib diisi.',
            'handover_photo.image' => 'File harus berupa gambar.',
            'handover_photo.max' => 'Ukuran foto maksimal 2MB.',
        ]);

        if ($request->hasFile('handover_photo')) {
            $validated['handover_photo'] = $request->file('handover_photo')->store('handover', 'public');
        }

        $borrowing->update([
            'status' => 'borrowed',
            'handover_date' => $validated['handover_date'],
            'handover_condition' => $validated['handover_condition'],
            'handover_photo' => $validated['handover_photo'] ?? $borrowing->handover_photo,
        ]);

        return redirect()
            ->route('borrowing.show', $borrowing)
            ->with('handover_recorded', 'Penyerahan barang berhasil dicatat.');
    }

    // F-11: Pencatatan Pengembalian
    public function returnForm(Request $request, Borrowing $borrowing)
    {
        abort_unless($this->isAdmin($request) || $this->ownsBorrowing($request, $borrowing), 403);
        abort_unless(in_array($borrowing->status, ['borrowed', 'overdue']), 404, 'Barang belum diserahkan atau sudah dikembalikan.');

        return view('borrowing.return', [
            'borrowing' => $borrowing,
            'conditions' => $this->conditionOptions(),
        ]);
    }

    public function recordReturn(Request $request, Borrowing $borrowing)
    {
        abort_unless($this->isAdmin($request) || $this->ownsBorrowing($request, $borrowing), 403);
        abort_unless(in_array($borrowing->status, ['borrowed', 'overdue']), 400, 'Status peminjaman tidak valid untuk pengembalian.');

        $minimumReturnDate = ($borrowing->handover_date ?: $borrowing->start_date)->format('Y-m-d');

        $validated = $request->validate([
            'return_date' => ['required', 'date', 'after_or_equal:' . $minimumReturnDate],
            'return_condition' => ['required', 'string', 'in:' . implode(',', ['good', 'fair', 'damaged', 'lost'])],
            'return_photo' => ['nullable', 'image', 'max:2048'],
            'damage_description' => ['required_if:return_condition,damaged,lost', 'nullable', 'string', 'max:1000'],
        ], [
            'return_date.required' => 'Tanggal pengembalian wajib diisi.',
            'return_date.after_or_equal' => 'Tanggal pengembalian tidak boleh sebelum barang dipinjam.',
            'return_condition.required' => 'Kondisi barang saat dikembalikan wajib diisi.',
            'return_photo.image' => 'File harus berupa gambar.',
            'return_photo.max' => 'Ukuran foto maksimal 2MB.',
            'damage_description.required_if' => 'Deskripsi kerusakan wajib diisi jika barang rusak atau hilang.',
        ]);

        if ($request->hasFile('return_photo')) {
            $validated['return_photo'] = $request->file('return_photo')->store('returns', 'public');
        }

        DB::transaction(function () use ($borrowing, $validated) {
            $fineAmount = $this->calculateFineForDates($borrowing->end_date, $validated['return_date']);

            $borrowing->update([
                'status' => 'returned',
                'return_date' => $validated['return_date'],
                'return_condition' => $validated['return_condition'],
                'return_photo' => $validated['return_photo'] ?? $borrowing->return_photo,
                'damage_description' => $validated['damage_description'] ?? null,
                'fine_amount' => $fineAmount,
            ]);

            // Kembalikan stok jika ada item_id
            if ($borrowing->item_id) {
                $item = $borrowing->item()->lockForUpdate()->first();

                if ($item) {
                    // Jika rusak/hilang, jangan kembalikan stok, ubah status item
                    if (in_array($validated['return_condition'], ['damaged', 'lost'])) {
                        $item->status = $validated['return_condition'] === 'lost' ? 'maintenance' : 'maintenance';
                    } else {
                        $item->quantity += 1;
                        $item->status = 'available';
                    }

                    $item->save();
                }
            }
        });

        return redirect()
            ->route('borrowing.show', $borrowing)
            ->with('return_recorded', 'Pengembalian barang berhasil dicatat.');
    }

    // F-14: Perpanjangan Pinjaman
    public function extensionForm(Request $request, Borrowing $borrowing)
    {
        if (! $this->isAdmin($request)) {
            $borrower = $this->borrowerFromSession($request);
            abort_unless($borrowing->borrower_nim === $borrower['nim'], 403);
        }

        abort_unless(in_array($borrowing->status, ['borrowed', 'overdue']), 404, 'Peminjaman tidak dalam status aktif.');

        return view('borrowing.extension', [
            'borrowing' => $borrowing,
        ]);
    }

    public function requestExtension(Request $request, Borrowing $borrowing)
    {
        $borrower = $this->borrowerFromSession($request);
        abort_unless($borrowing->borrower_nim === $borrower['nim'], 403);
        abort_unless(in_array($borrowing->status, ['borrowed', 'overdue']), 400, 'Peminjaman tidak dalam status aktif.');
        abort_if($borrowing->extension_requested, 400, 'Perpanjangan sudah diajukan, menunggu persetujuan.');

        $validated = $request->validate([
            'extension_new_date' => ['required', 'date', 'after:' . $borrowing->end_date->format('Y-m-d')],
            'extension_reason' => ['required', 'string', 'max:500'],
        ], [
            'extension_new_date.required' => 'Tanggal perpanjangan wajib diisi.',
            'extension_new_date.after' => 'Tanggal perpanjangan harus setelah tanggal jatuh tempo.',
            'extension_reason.required' => 'Alasan perpanjangan wajib diisi.',
        ]);

        $borrowing->update([
            'extension_requested' => true,
            'extension_new_date' => $validated['extension_new_date'],
            'extension_reason' => $validated['extension_reason'],
            'extension_rejection_reason' => null,
            'extension_rejected_at' => null,
        ]);

        return redirect()
            ->route('borrowing.show', $borrowing)
            ->with('extension_requested', 'Permintaan perpanjangan berhasil diajukan, menunggu persetujuan admin.');
    }

    public function approveExtension(Request $request, Borrowing $borrowing)
    {
        abort_unless($this->isAdmin($request), 403);
        abort_unless($borrowing->extension_requested, 400, 'Tidak ada permintaan perpanjangan.');

        $borrowing->update([
            'end_date' => $borrowing->extension_new_date,
            'status' => $borrowing->status === 'overdue' ? 'borrowed' : $borrowing->status,
            'extension_requested' => false,
            'extension_new_date' => null,
            'extension_reason' => null,
            'extension_rejection_reason' => null,
            'extension_rejected_at' => null,
            'admin_note' => 'Perpanjangan disetujui hingga ' . $borrowing->extension_new_date->format('d M Y') . '.',
        ]);

        return back()->with('extension_approved', 'Perpanjangan peminjaman disetujui.');
    }

    public function rejectExtension(Request $request, Borrowing $borrowing)
    {
        abort_unless($this->isAdmin($request), 403);
        abort_unless($borrowing->extension_requested, 400, 'Tidak ada permintaan perpanjangan.');

        $validated = $request->validate([
            'extension_rejection_reason' => ['required', 'string', 'max:500'],
        ], [
            'extension_rejection_reason.required' => 'Alasan penolakan perpanjangan wajib diisi.',
        ]);

        $borrowing->update([
            'extension_requested' => false,
            'extension_new_date' => null,
            'extension_reason' => null,
            'extension_rejection_reason' => $validated['extension_rejection_reason'],
            'extension_rejected_at' => now(),
        ]);

        return back()->with('extension_rejected', 'Perpanjangan peminjaman ditolak.');
    }

    // Task 19: Catatan Berkala Peminjaman (Notulensi #12)
    public function storeNote(Request $request, Borrowing $borrowing)
    {
        $validated = $request->validate([
            'content' => ['required', 'string', 'max:2000'],
        ], [
            'content.required' => 'Isi catatan wajib diisi.',
        ]);

        BorrowingNote::create([
            'borrowing_id' => $borrowing->id,
            'user_id' => $request->user()?->id,
            'content' => $validated['content'],
        ]);

        return back()->with('note_added', 'Catatan berhasil ditambahkan.');
    }

    public function deleteNote(BorrowingNote $note)
    {
        $note->delete();

        return back()->with('note_deleted', 'Catatan berhasil dihapus.');
    }

    // Task 18: Pengecekan Barang Pra-Pengembalian (Notulensi #7)
    public function preReturnCheck(Request $request, Borrowing $borrowing)
    {
        abort_unless(in_array($borrowing->status, ['borrowed', 'overdue']), 400, 'Status peminjaman tidak valid untuk pengecekan.');

        $minimumCheckDate = ($borrowing->handover_date ?: $borrowing->start_date)->format('Y-m-d');

        $validated = $request->validate([
            'pre_return_condition' => ['required', 'string', 'in:good,fair,damaged'],
            'pre_return_check_date' => ['required', 'date', 'after_or_equal:' . $minimumCheckDate, 'before_or_equal:today'],
        ], [
            'pre_return_condition.required' => 'Kondisi barang wajib diisi.',
            'pre_return_check_date.required' => 'Tanggal pengecekan wajib diisi.',
            'pre_return_check_date.after_or_equal' => 'Tanggal pra-pengembalian tidak boleh sebelum barang dipinjam.',
        ]);

        $borrowing->update($validated);

        return back()->with('pre_return_checked', 'Pengecekan pra-pengembalian berhasil dicatat.');
    }

    // Task 17: Hitung Denda Keterlambatan (Notulensi #4)
    public function calculateFine(Borrowing $borrowing): float
    {
        if (! in_array($borrowing->status, ['borrowed', 'overdue', 'returned'])) {
            return 0;
        }

        return $this->calculateFineForDates(
            $borrowing->end_date,
            $borrowing->return_date ?: now()
        );
    }

    private function calculateFineForDates($dueDate, $returnDate): float
    {
        $dueDate = \Illuminate\Support\Carbon::parse($dueDate)->startOfDay();
        $returnDate = \Illuminate\Support\Carbon::parse($returnDate)->startOfDay();

        $daysLate = max(0, (int) $dueDate->diffInDays($returnDate, false));

        if ($daysLate <= 0) {
            return 0;
        }

        // Denda Rp 5.000 per hari keterlambatan
        return $daysLate * 5000;
    }

    private function borrowerFromSession(Request $request): array
    {
        $user = $request->user();
        $sessionUser = $request->session()->get('user', []);

        return [
            'name' => data_get($user, 'name')
                ?? data_get($sessionUser, 'name')
                ?? $request->session()->get('name')
                ?? 'Nama pengguna belum tersedia',
            'nim' => data_get($user, 'nim')
                ?? data_get($sessionUser, 'nim')
                ?? data_get($sessionUser, 'student_id')
                ?? $request->session()->get('nim')
                ?? 'NIM belum tersedia',
        ];
    }

    private function isAvailable(Item $item): bool
    {
        return $item->status === 'available' && $item->quantity > 0;
    }

    private function syncInventoryForStatusChange(Borrowing $borrowing, string $oldStatus, string $newStatus): void
    {
        $wasHoldingInventory = $this->holdsInventory($oldStatus);
        $willHoldInventory = $this->holdsInventory($newStatus);

        if ($wasHoldingInventory === $willHoldInventory || ! $borrowing->item_id) {
            return;
        }

        $item = $borrowing->item()->lockForUpdate()->first();

        if (! $item) {
            return;
        }

        if ($willHoldInventory) {
            if ($item->status === 'maintenance' || $item->quantity < 1) {
                throw ValidationException::withMessages([
                    'status' => 'Stok barang tidak tersedia untuk menyetujui peminjaman ini.',
                ]);
            }

            $item->quantity -= 1;
            $item->status = $item->quantity > 0 ? 'available' : 'borrowed';
            $item->save();

            return;
        }

        $item->quantity += 1;

        if ($item->status !== 'maintenance') {
            $item->status = 'available';
        }

        $item->save();
    }

    private function holdsInventory(string $status): bool
    {
        return in_array($status, self::INVENTORY_HOLD_STATUSES, true);
    }

    private function whatsappUrl(string $message): string
    {
        $number = preg_replace('/\D+/', '', (string) config('services.whatsapp.sherizka_number'));
        $baseUrl = $number ? "https://wa.me/{$number}" : 'https://wa.me/';

        return $baseUrl . '?text=' . rawurlencode($message);
    }

    private function statusOptions(): array
    {
        return [
            'pending' => 'Menunggu',
            'approved' => 'Siap Diambil',
            'rejected' => 'Ditolak',
            'borrowed' => 'Dipinjam',
            'returned' => 'Dikembalikan',
            'overdue' => 'Terlambat',
        ];
    }

    private function conditionOptions(): array
    {
        return [
            'good' => 'Baik',
            'fair' => 'Layak Pakai',
            'damaged' => 'Rusak',
        ];
    }

    private function isAdmin(Request $request): bool
    {
        $role = $request->user()?->role
            ?? data_get($request->session()->get('user', []), 'role');

        return in_array(strtolower((string) $role), ['admin', 'operator'], true);
    }

    private function ownsBorrowing(Request $request, Borrowing $borrowing): bool
    {
        $borrower = $this->borrowerFromSession($request);

        return filled($borrower['nim']) && $borrowing->borrower_nim === $borrower['nim'];
    }
}
