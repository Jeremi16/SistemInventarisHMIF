<?php

namespace App\Http\Controllers;

use App\Models\Borrowing;
use App\Models\Item;
use App\Models\ItemIncoming;
use App\Models\ItemOutgoing;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        $summary = [
            'total_items' => Item::query()->sum('quantity'),
            'available_items' => Item::query()->where('status', 'available')->sum('quantity'),
            'active_borrowings' => Borrowing::query()->whereIn('status', ['approved', 'borrowed', 'overdue'])->count(),
            'overdue_borrowings' => Borrowing::query()->where('status', 'overdue')->count(),
        ];

        return view('reports.index', compact('summary'));
    }

    /**
     * F-15: Laporan Stok Terkini
     */
    public function stockReport(Request $request)
    {
        $items = Item::query()
            ->withCount(['borrowings' => fn ($q) => $q->whereIn('status', ['approved', 'borrowed', 'overdue'])])
            ->when($request->filled('category'), fn ($q) => $q->where('category', $request->category))
            ->when($request->filled('condition'), fn ($q) => $q->where('condition', $request->condition))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('search'), fn ($q) => $q->where('name', 'like', '%' . $request->search . '%'))
            ->orderBy('name')
            ->paginate(20);

        $categories = Item::distinct()->pluck('category');

        $summary = [
            'total_items' => Item::query()->sum('quantity'),
            'total_available' => Item::query()->where('status', 'available')->sum('quantity'),
            'total_borrowed' => Item::query()->where('status', 'borrowed')->sum('quantity'),
            'total_maintenance' => Item::query()->where('status', 'maintenance')->sum('quantity'),
            'active_borrowings' => Borrowing::query()->whereIn('status', ['approved', 'borrowed', 'overdue'])->count(),
        ];

        return view('reports.stock', compact('items', 'categories', 'summary'));
    }

    /**
     * F-17: Laporan Peminjaman
     */
    public function borrowingReport(Request $request)
    {
        $borrowings = Borrowing::query()
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('date_from'), fn ($q) => $q->whereDate('start_date', '>=', $request->date_from))
            ->when($request->filled('date_to'), fn ($q) => $q->whereDate('start_date', '<=', $request->date_to))
            ->when($request->filled('search'), fn ($q) => $q->where(function ($query) use ($request) {
                $query->where('item_name', 'like', '%' . $request->search . '%')
                    ->orWhere('borrower_name', 'like', '%' . $request->search . '%')
                    ->orWhere('borrower_nim', 'like', '%' . $request->search . '%');
            }))
            ->latest()
            ->paginate(20);

        $stats = [
            'total' => Borrowing::query()->when($request->filled('date_from'), fn ($q) => $q->whereDate('start_date', '>=', $request->date_from))
                ->when($request->filled('date_to'), fn ($q) => $q->whereDate('start_date', '<=', $request->date_to))
                ->count(),
            'pending' => Borrowing::query()->where('status', 'pending')->count(),
            'approved' => Borrowing::query()->where('status', 'approved')->count(),
            'borrowed' => Borrowing::query()->whereIn('status', ['borrowed', 'overdue'])->count(),
            'returned' => Borrowing::query()->where('status', 'returned')->count(),
            'rejected' => Borrowing::query()->where('status', 'rejected')->count(),
        ];

        // Frekuensi peminjaman per barang
        $topItems = Borrowing::query()
            ->selectRaw('item_name, COUNT(*) as total')
            ->when($request->filled('date_from'), fn ($q) => $q->whereDate('start_date', '>=', $request->date_from))
            ->when($request->filled('date_to'), fn ($q) => $q->whereDate('start_date', '<=', $request->date_to))
            ->groupBy('item_name')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        // Frekuensi peminjaman per peminjam
        $topBorrowers = Borrowing::query()
            ->selectRaw('borrower_name, borrower_nim, COUNT(*) as total')
            ->when($request->filled('date_from'), fn ($q) => $q->whereDate('start_date', '>=', $request->date_from))
            ->when($request->filled('date_to'), fn ($q) => $q->whereDate('start_date', '<=', $request->date_to))
            ->groupBy('borrower_name', 'borrower_nim')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        return view('reports.borrowing', compact('borrowings', 'stats', 'topItems', 'topBorrowers'));
    }

    /**
     * F-18: Laporan Kondisi Barang
     */
    public function conditionReport(Request $request)
    {
        $items = Item::query()
            ->when($request->filled('category'), fn ($q) => $q->where('category', $request->category))
            ->orderBy('name')
            ->get();

        $conditionSummary = [
            'good' => $items->where('condition', 'good')->count(),
            'fair' => $items->where('condition', 'fair')->count(),
            'damaged' => $items->where('condition', 'damaged')->count(),
        ];

        $statusSummary = [
            'available' => $items->where('status', 'available')->count(),
            'borrowed' => $items->where('status', 'borrowed')->count(),
            'maintenance' => $items->where('status', 'maintenance')->count(),
        ];

        $categories = Item::distinct()->pluck('category');

        return view('reports.condition', compact('items', 'conditionSummary', 'statusSummary', 'categories'));
    }

    /**
     * F-16: Histori Barang Masuk/Keluar
     */
    public function transactionHistory(Request $request)
    {
        // Barang Masuk
        $incomings = ItemIncoming::query()
            ->with('item')
            ->when($request->filled('source'), fn ($q) => $q->where('source', $request->source))
            ->when($request->filled('date_from'), fn ($q) => $q->whereDate('date', '>=', $request->date_from))
            ->when($request->filled('date_to'), fn ($q) => $q->whereDate('date', '<=', $request->date_to))
            ->latest()
            ->limit(50)
            ->get()
            ->map(fn ($row) => [
                'type' => 'Masuk',
                'item_name' => $row->item->name ?? '-',
                'date' => $row->date->format('d M Y'),
                'raw_date' => $row->date,
                'quantity' => $row->quantity,
                'detail' => ucfirst($row->source),
                'notes' => $row->notes,
            ]);

        // Barang Keluar
        $outgoings = ItemOutgoing::query()
            ->with('item')
            ->when($request->filled('reason'), fn ($q) => $q->where('reason', $request->reason))
            ->when($request->filled('date_from'), fn ($q) => $q->whereDate('date', '>=', $request->date_from))
            ->when($request->filled('date_to'), fn ($q) => $q->whereDate('date', '<=', $request->date_to))
            ->latest()
            ->limit(50)
            ->get()
            ->map(fn ($row) => [
                'type' => 'Keluar',
                'item_name' => $row->item->name ?? '-',
                'date' => $row->date->format('d M Y'),
                'raw_date' => $row->date,
                'quantity' => $row->quantity,
                'detail' => ucfirst($row->reason),
                'notes' => $row->notes,
            ]);

        // Merge sort by date descending
        $transactions = $incomings->concat($outgoings)
            ->sortByDesc('raw_date')
            ->values()
            ->all();

        // Pivot untuk pagination manual
        $page = (int) ($request->get('page', 1));
        $perPage = 20;
        $total = count($transactions);
        $transactions = array_slice($transactions, ($page - 1) * $perPage, $perPage);

        return view('reports.history', [
            'transactions' => $transactions,
            'total' => $total,
            'page' => $page,
            'perPage' => $perPage,
            'sources' => ['pembelian', 'donasi', 'hibah'],
            'reasons' => ['penghapusan', 'kerusakan', 'pemindahan'],
        ]);
    }

    /**
     * F-19: Export Laporan Stok (PDF Printable View)
     */
    public function exportStockPdf()
    {
        $items = Item::query()->orderBy('name')->get();
        $summary = [
            'total_items' => Item::query()->sum('quantity'),
            'total_available' => Item::query()->where('status', 'available')->sum('quantity'),
            'total_borrowed' => Item::query()->where('status', 'borrowed')->sum('quantity'),
            'total_maintenance' => Item::query()->where('status', 'maintenance')->sum('quantity'),
        ];

        return view('reports.exports.stock-pdf', compact('items', 'summary'));
    }

    /**
     * F-19: Export Laporan Peminjaman (PDF Printable View)
     */
    public function exportBorrowingPdf(Request $request)
    {
        $borrowings = Borrowing::query()
            ->when($request->filled('date_from'), fn ($q) => $q->whereDate('start_date', '>=', $request->date_from))
            ->when($request->filled('date_to'), fn ($q) => $q->whereDate('start_date', '<=', $request->date_to))
            ->latest()
            ->get();

        $stats = [
            'total' => $borrowings->count(),
            'returned' => $borrowings->where('status', 'returned')->count(),
            'pending' => $borrowings->where('status', 'pending')->count(),
            'active' => $borrowings->whereIn('status', ['borrowed', 'overdue', 'approved'])->count(),
        ];

        return view('reports.exports.borrowing-pdf', compact('borrowings', 'stats'));
    }
}
