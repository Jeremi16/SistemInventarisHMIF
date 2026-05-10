<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class BorrowingController extends Controller
{
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
                'whatsapp_url' => $this->whatsappUrl($message),
            ]);
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

    private function whatsappUrl(string $message): string
    {
        $number = preg_replace('/\D+/', '', (string) config('services.whatsapp.sherizka_number'));
        $baseUrl = $number ? "https://wa.me/{$number}" : 'https://wa.me/';

        return $baseUrl . '?text=' . rawurlencode($message);
    }
}
