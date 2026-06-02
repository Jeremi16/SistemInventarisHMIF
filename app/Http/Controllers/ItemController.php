<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    private const ITEM_STATUSES = ['available', 'borrowed', 'maintenance'];
    private const ITEM_CONDITIONS = ['good', 'fair', 'damaged'];

    public function index(Request $request)
    {
        $items = Item::query()
            ->search($request->search)
            ->byCategory($request->category)
            ->byStatus($request->status)
            ->byCondition($request->condition)
            ->latest()
            ->paginate(12);

        $categories = Item::distinct()->pluck('category');

        return view('inventory.index', compact('items', 'categories'));
    }

    public function create()
    {
        return view('inventory.create', [
            'statuses' => $this->statusOptions(),
            'conditions' => $this->conditionOptions(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
            'quantity' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'string', 'in:' . implode(',', self::ITEM_STATUSES)],
            'condition' => ['required', 'string', 'in:' . implode(',', self::ITEM_CONDITIONS)],
            'location' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'photo' => ['nullable', 'image', 'max:2048'],
        ], [
            'name.required' => 'Nama barang wajib diisi.',
            'category.required' => 'Kategori wajib diisi.',
            'quantity.required' => 'Jumlah stok wajib diisi.',
            'quantity.min' => 'Jumlah stok tidak boleh negatif.',
            'status.in' => 'Status barang tidak valid.',
            'condition.in' => 'Kondisi barang tidak valid.',
            'photo.image' => 'File harus berupa gambar.',
            'photo.max' => 'Ukuran foto maksimal 2MB.',
        ]);

        if ((int) $validated['quantity'] === 0 && $validated['status'] === 'available') {
            $validated['status'] = 'borrowed';
        }

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('items', 'public');
        }

        $item = Item::create($validated);

        return redirect()
            ->route('inventory.show', $item)
            ->with('item_created', 'Barang baru berhasil ditambahkan.');
    }

    public function show(Item $item)
    {
        return view('inventory.show', [
            'item' => $item,
            'statuses' => $this->statusOptions(),
            'conditions' => $this->conditionOptions(),
        ]);
    }

    public function edit(Item $item)
    {
        return view('inventory.edit', [
            'item' => $item,
            'statuses' => $this->statusOptions(),
            'conditions' => $this->conditionOptions(),
        ]);
    }

    public function update(Request $request, Item $item)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
            'quantity' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'string', 'in:' . implode(',', self::ITEM_STATUSES)],
            'condition' => ['required', 'string', 'in:' . implode(',', self::ITEM_CONDITIONS)],
            'location' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'photo' => ['nullable', 'image', 'max:2048'],
        ], [
            'name.required' => 'Nama barang wajib diisi.',
            'category.required' => 'Kategori wajib diisi.',
            'quantity.required' => 'Jumlah stok wajib diisi.',
            'quantity.min' => 'Jumlah stok tidak boleh negatif.',
            'status.in' => 'Status barang tidak valid.',
            'condition.in' => 'Kondisi barang tidak valid.',
            'photo.image' => 'File harus berupa gambar.',
            'photo.max' => 'Ukuran foto maksimal 2MB.',
        ]);

        if ((int) $validated['quantity'] === 0 && $validated['status'] === 'available') {
            $validated['status'] = 'borrowed';
        }

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('items', 'public');
        }

        $item->update($validated);

        return redirect()
            ->route('inventory.show', $item)
            ->with('item_updated', 'Informasi barang berhasil diperbarui.');
    }

    public function destroy(Item $item)
    {
        $item->delete();

        return redirect()
            ->route('inventory.index')
            ->with('item_deleted', 'Barang berhasil dihapus.');
    }

    // F-07: QR Code Barang
    public function qrCode(Item $item)
    {
        $qrData = route('inventory.show', $item);
        $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($qrData);

        return view('inventory.qrcode', [
            'item' => $item,
            'qrUrl' => $qrUrl,
        ]);
    }

    // F-07: Download QR Code
    public function downloadQrCode(Item $item)
    {
        $qrData = route('inventory.show', $item);
        $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=500x500&data=' . urlencode($qrData);

        return redirect()->away($qrUrl);
    }

    public function catalog(Request $request)
    {
        $items = Item::query()
            ->search($request->search)
            ->byCategory($request->category)
            ->byStatus($request->status)
            ->byCondition($request->condition)
            ->latest()
            ->paginate(12);

        $dbCategories = Item::distinct()->pluck('category')->toArray();
        $categories = array_unique(array_merge(['Electronics', 'Event Gear', 'Office Supplies'], $dbCategories));

        return view('catalog.index', compact('items', 'categories'));
    }

    private function statusOptions(): array
    {
        return [
            'available' => 'Tersedia',
            'borrowed' => 'Dipinjam',
            'maintenance' => 'Maintenance',
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
}
