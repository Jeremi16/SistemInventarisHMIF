<?php

namespace App\Http\Controllers;

use App\Models\Borrowing;
use App\Models\Item;
use Illuminate\Http\Request;
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
            'end_date.after_or_equal' => 'Tanggal pengembalian harus sama atau setelah tanggal mulai.',
            'purpose.min' => 'Keperluan peminjaman minimal 10 karakter.',
            'terms_accepted.accepted' => 'Anda harus menyetujui syarat dan ketentuan peminjaman.',
        ]);

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
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
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
        if (! $this->isAdmin($request)) {
            $borrower = $this->borrowerFromSession($request);

            abort_unless($borrowing->borrower_nim === $borrower['nim'], 403);
        }

        return view('borrowing.show', [
            'borrowing' => $borrowing,
            'statusLabel' => $this->statusOptions()[$borrowing->status] ?? 'Menunggu',
            'isAdmin' => $this->isAdmin($request),
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
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            'borrowed' => 'Dipinjam',
            'returned' => 'Dikembalikan',
            'overdue' => 'Terlambat',
        ];
    }

    private function isAdmin(Request $request): bool
    {
        $role = $request->user()?->role
            ?? data_get($request->session()->get('user', []), 'role');

        return strtolower((string) $role) === 'admin';
    }
}
