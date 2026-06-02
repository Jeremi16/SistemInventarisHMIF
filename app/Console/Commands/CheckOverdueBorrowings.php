<?php

namespace App\Console\Commands;

use App\Models\Borrowing;
use Illuminate\Console\Command;

class CheckOverdueBorrowings extends Command
{
    protected $signature = 'borrowing:check-overdue';
    protected $description = 'Cek peminjaman yang melewati jatuh tempo dan tandai sebagai overdue';

    public function handle(): int
    {
        $now = now()->startOfDay();

        // Tandai overdue: status borrowed & end_date < hari ini
        $overdue = Borrowing::query()
            ->where('status', 'borrowed')
            ->whereDate('end_date', '<', $now)
            ->update(['status' => 'overdue']);

        $this->info("{$overdue} peminjaman ditandai sebagai terlambat.");

        // Cek H-1 (besok jatuh tempo)
        $dueTomorrow = Borrowing::query()
            ->whereIn('status', ['borrowed', 'approved'])
            ->whereDate('end_date', '=', $now->copy()->addDay())
            ->get();

        foreach ($dueTomorrow as $borrowing) {
            $this->line("H-1: \"{$borrowing->item_name}\" oleh {$borrowing->borrower_name} (NIM {$borrowing->borrower_nim}) jatuh tempo besok.");
        }

        // Cek H+0 (hari ini jatuh tempo)
        $dueToday = Borrowing::query()
            ->whereIn('status', ['borrowed', 'approved'])
            ->whereDate('end_date', '=', $now)
            ->get();

        foreach ($dueToday as $borrowing) {
            $this->line("H+0: \"{$borrowing->item_name}\" oleh {$borrowing->borrower_name} (NIM {$borrowing->borrower_nim}) jatuh tempo hari ini.");
        }

        $this->info('Pengecekan selesai.');

        return self::SUCCESS;
    }
}
