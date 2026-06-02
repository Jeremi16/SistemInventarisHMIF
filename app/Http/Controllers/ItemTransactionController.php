<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ItemIncoming;
use App\Models\ItemOutgoing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ItemTransactionController extends Controller
{
    // ========== Barang Masuk (F-04) ==========

    public function incomingIndex(Request $request)
    {
        $incomings = ItemIncoming::query()
            ->with('item')
            ->when($request->filled('source'), fn ($q) => $q->where('source', $request->source))
            ->when($request->filled('date_from'), fn ($q) => $q->whereDate('date', '>=', $request->date_from))
            ->when($request->filled('date_to'), fn ($q) => $q->whereDate('date', '<=', $request->date_to))
            ->latest()
            ->paginate(15);

        return view('incoming.index', [
            'incomings' => $incomings,
            'sources' => ['pembelian', 'donasi', 'hibah'],
        ]);
    }

    public function incomingCreate()
    {
        return view('incoming.create', [
            'items' => Item::query()->orderBy('name')->get(),
            'sources' => ['pembelian', 'donasi', 'hibah'],
        ]);
    }

    public function incomingStore(Request $request)
    {
        $validated = $request->validate([
            'item_id' => ['required', 'integer', 'exists:items,id'],
            'source' => ['required', 'string', 'in:pembelian,donasi,hibah'],
            'date' => ['required', 'date', 'before_or_equal:today'],
            'quantity' => ['required', 'integer', 'min:1'],
            'proof_file' => ['nullable', 'file', 'mimes:pdf,jpg,png', 'max:2048'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ], [
            'item_id.required' => 'Barang wajib dipilih.',
            'source.required' => 'Sumber pengadaan wajib dipilih.',
            'date.required' => 'Tanggal pengadaan wajib diisi.',
            'quantity.required' => 'Jumlah wajib diisi.',
            'quantity.min' => 'Jumlah minimal 1.',
        ]);

        DB::transaction(function () use ($validated, $request) {
            if ($request->hasFile('proof_file')) {
                $validated['proof_file'] = $request->file('proof_file')->store('incoming', 'public');
            }

            ItemIncoming::create($validated);

            // Update stok item
            $item = Item::findOrFail($validated['item_id']);
            $item->quantity += $validated['quantity'];

            if ($item->status === 'borrowed' && $item->quantity > 0) {
                $item->status = 'available';
            }

            $item->save();
        });

        return redirect()
            ->route('incoming.index')
            ->with('incoming_created', 'Barang masuk berhasil dicatat.');
    }

    // ========== Barang Keluar (F-05) ==========

    public function outgoingIndex(Request $request)
    {
        $outgoings = ItemOutgoing::query()
            ->with('item')
            ->when($request->filled('reason'), fn ($q) => $q->where('reason', $request->reason))
            ->when($request->filled('date_from'), fn ($q) => $q->whereDate('date', '>=', $request->date_from))
            ->when($request->filled('date_to'), fn ($q) => $q->whereDate('date', '<=', $request->date_to))
            ->latest()
            ->paginate(15);

        return view('outgoing.index', [
            'outgoings' => $outgoings,
            'reasons' => ['penghapusan', 'kerusakan', 'pemindahan'],
        ]);
    }

    public function outgoingCreate()
    {
        return view('outgoing.create', [
            'items' => Item::query()->orderBy('name')->get(),
            'reasons' => ['penghapusan', 'kerusakan', 'pemindahan'],
        ]);
    }

    public function outgoingStore(Request $request)
    {
        $validated = $request->validate([
            'item_id' => ['required', 'integer', 'exists:items,id'],
            'reason' => ['required', 'string', 'in:penghapusan,kerusakan,pemindahan'],
            'date' => ['required', 'date', 'before_or_equal:today'],
            'quantity' => ['required', 'integer', 'min:1'],
            'documentation_file' => ['nullable', 'file', 'mimes:pdf,jpg,png', 'max:2048'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ], [
            'item_id.required' => 'Barang wajib dipilih.',
            'reason.required' => 'Alasan pengeluaran wajib dipilih.',
            'date.required' => 'Tanggal pengeluaran wajib diisi.',
            'quantity.required' => 'Jumlah wajib diisi.',
            'quantity.min' => 'Jumlah minimal 1.',
        ]);

        DB::transaction(function () use ($validated, $request) {
            $item = Item::lockForUpdate()->findOrFail($validated['item_id']);

            if ($validated['quantity'] > $item->quantity) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'quantity' => 'Jumlah barang keluar melebihi stok tersedia.',
                ]);
            }

            if ($request->hasFile('documentation_file')) {
                $validated['documentation_file'] = $request->file('documentation_file')->store('outgoing', 'public');
            }

            ItemOutgoing::create($validated);

            // Update stok item
            $item->quantity -= $validated['quantity'];

            if ($item->quantity === 0) {
                $item->status = 'maintenance';
            }

            $item->save();
        });

        return redirect()
            ->route('outgoing.index')
            ->with('outgoing_created', 'Barang keluar berhasil dicatat.');
    }
}
