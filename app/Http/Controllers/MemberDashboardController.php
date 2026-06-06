<?php

namespace App\Http\Controllers;

use App\Models\Borrowing;
use App\Models\Item;
use Illuminate\Http\Request;

class MemberDashboardController extends Controller
{
    private const STATUS_LABELS = [
        'pending' => 'Menunggu',
        'rejected' => 'Ditolak',
        'borrowed' => 'Diterima',
        'returned' => 'Dikembalikan',
        'overdue' => 'Terlambat',
    ];

    public function __invoke(Request $request)
    {
        $sessionUser = $request->session()->get('user', []);
        $member = [
            'name' => $request->user()?->name
                ?? data_get($sessionUser, 'name')
                ?? 'Anggota HMIF',
            'nim' => $request->user()?->nim
                ?? data_get($sessionUser, 'nim')
                ?? 'NIM belum tersedia',
        ];

        $availableItems = Item::query()
            ->where('status', 'available')
            ->where('quantity', '>', 0)
            ->latest()
            ->take(4)
            ->get();

        $memberBorrowings = Borrowing::query()
            ->where('borrower_nim', $member['nim'])
            ->latest()
            ->get();

        $stats = [
            'available_items' => Item::query()
                ->where('status', 'available')
                ->sum('quantity'),
            'active_requests' => $memberBorrowings
                ->whereIn('status', ['pending', 'approved', 'borrowed', 'overdue'])
                ->count(),
            'pending_requests' => $memberBorrowings
                ->where('status', 'pending')
                ->count(),
            'nearest_due' => $this->nearestDueLabel($memberBorrowings),
        ];

        $borrowings = $memberBorrowings
            ->map(fn (Borrowing $borrowing) => [
                'id' => $borrowing->id,
                'item' => $borrowing->item_name,
                'status' => self::STATUS_LABELS[$borrowing->status] ?? 'Menunggu',
                'date' => $borrowing->start_date->translatedFormat('d M Y H:i:s'),
                'due' => $borrowing->end_date->translatedFormat('d M Y H:i:s'),
                'note' => $borrowing->admin_note ?: 'Menunggu catatan dan verifikasi admin.',
            ])
            ->values()
            ->all();

        $fallbackItems = [
            [
                'name' => 'Laptop ASUS',
                'category' => 'Elektronik',
                'quantity' => 2,
                'location' => 'Lemari A',
            ],
            [
                'name' => 'Speaker JBL',
                'category' => 'Event Gear',
                'quantity' => 1,
                'location' => 'Lemari B',
            ],
            [
                'name' => 'Kabel HDMI',
                'category' => 'Aksesoris',
                'quantity' => 5,
                'location' => 'Laci 1',
            ],
        ];

        return view('member.dashboard', [
            'member' => $member,
            'stats' => $stats,
            'borrowings' => $borrowings,
            'availableItems' => $availableItems,
            'fallbackItems' => $fallbackItems,
        ]);
    }

    private function nearestDueLabel($borrowings): string
    {
        $nearest = $borrowings
            ->whereIn('status', ['approved', 'borrowed', 'overdue'])
            ->sortBy('end_date')
            ->first();

        if (! $nearest) {
            return '-';
        }

        $days = now()->startOfDay()->diffInDays($nearest->end_date->startOfDay(), false);

        if ($days < 0) {
            return 'Terlambat';
        }

        return $days === 0 ? 'Hari ini' : "{$days} hari";
    }
}
