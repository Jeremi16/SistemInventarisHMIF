<?php

namespace Tests\Feature;

use App\Models\Borrowing;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardStatsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_stats_are_realtime_from_database(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        Item::create([
            'name' => 'Laptop ASUS',
            'category' => 'Elektronik',
            'status' => 'available',
            'quantity' => 3,
            'condition' => 'good',
        ]);

        Item::create([
            'name' => 'Kamera Canon',
            'category' => 'Elektronik',
            'status' => 'maintenance',
            'quantity' => 1,
            'condition' => 'damaged',
        ]);

        Borrowing::create([
            'item_name' => 'Laptop ASUS',
            'borrower_name' => 'Ayu Pratiwi',
            'borrower_nim' => '121140001',
            'start_date' => now()->subDays(2)->toDateString(),
            'end_date' => now()->addDay()->toDateString(),
            'purpose' => 'Digunakan untuk rapat kerja HMIF.',
            'status' => 'borrowed',
        ]);

        Borrowing::create([
            'item_name' => 'Speaker JBL',
            'borrower_name' => 'Bima Santoso',
            'borrower_nim' => '121140099',
            'start_date' => now()->subDays(5)->toDateString(),
            'end_date' => now()->subDay()->toDateString(),
            'purpose' => 'Digunakan untuk kegiatan HMIF.',
            'status' => 'overdue',
        ]);

        $this->actingAs($admin)->get(route('dashboard'))
            ->assertOk()
            ->assertSeeInOrder(['Total Barang', '4'])
            ->assertSeeInOrder(['Sedang Dipinjam', '2'])
            ->assertSeeInOrder(['Butuh Perawatan', '1'])
            ->assertSeeInOrder(['Terlambat', '1']);
    }
}
