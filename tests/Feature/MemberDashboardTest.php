<?php

namespace Tests\Feature;

use App\Models\Borrowing;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class MemberDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_is_available_for_members(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('Login Anggota')
            ->assertSee('Masuk ke akun member');
    }

    public function test_member_can_login_with_nim_and_reach_member_dashboard(): void
    {
        $user = User::factory()->create([
            'name' => 'Ayu Pratiwi',
            'email' => 'ayu@hmif.itera.ac.id',
            'nim' => '121140001',
            'password' => Hash::make('secret-password'),
            'role' => 'member',
        ]);

        $this->post(route('login.attempt'), [
            'identifier' => '121140001',
            'password' => 'secret-password',
        ])
            ->assertRedirect(route('member.dashboard'))
            ->assertSessionHas('user.nim', '121140001');

        $this->assertAuthenticatedAs($user);
    }

    public function test_admin_login_redirects_to_admin_dashboard(): void
    {
        $user = User::factory()->create([
            'name' => 'Sherizka',
            'email' => 'sherizka@hmif.itera.ac.id',
            'nim' => '121140002',
            'password' => Hash::make('secret-password'),
            'role' => 'admin',
        ]);

        $this->post(route('login.attempt'), [
            'identifier' => 'sherizka@hmif.itera.ac.id',
            'password' => 'secret-password',
        ])
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('user.role', 'admin');

        $this->assertAuthenticatedAs($user);
    }

    public function test_member_dashboard_shows_member_pov(): void
    {
        $member = User::factory()->create([
            'name' => 'Ayu Pratiwi',
            'nim' => '121140001',
            'role' => 'member',
        ]);

        Item::create([
            'name' => 'Laptop ASUS',
            'category' => 'Elektronik',
            'status' => 'available',
            'quantity' => 2,
            'condition' => 'good',
            'location' => 'Lemari A',
        ]);

        $borrowing = Borrowing::create([
            'item_name' => 'Laptop ASUS',
            'borrower_name' => 'Ayu Pratiwi',
            'borrower_nim' => '121140001',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(2)->toDateString(),
            'purpose' => 'Digunakan untuk rapat kerja HMIF.',
            'status' => 'approved',
            'admin_note' => 'Silakan ambil barang di sekretariat HMIF.',
        ]);

        $this->actingAs($member)->withSession([
            'user' => [
                'name' => 'Ayu Pratiwi',
                'nim' => '121140001',
                'role' => 'member',
            ],
        ])->get(route('member.dashboard'))
            ->assertOk()
            ->assertSee('Status Permintaan Terkini')
            ->assertSee('Laptop ASUS')
            ->assertSee('Siap Diambil')
            ->assertSee('Silakan ambil barang di sekretariat HMIF.')
            ->assertSee('Notifikasi Peminjaman')
            ->assertSee('Status diubah menjadi Siap Diambil.')
            ->assertSee('id="notification-badge"', false)
            ->assertSee('data-notification-key=', false)
            ->assertSee('data-theme-option="light"', false)
            ->assertSee('data-theme-option="dark"', false)
            ->assertDontSee('Pengaturan')
            ->assertSee('Peminjaman Saya')
            ->assertSee('Barang Siap Dipinjam')
            ->assertSee(route('borrowing.show', $borrowing), false)
            ->assertSee('Laptop ASUS')
            ->assertSee('121140001');

        $this->actingAs($member)->withSession([
            'user' => [
                'name' => 'Ayu Pratiwi',
                'nim' => '121140001',
                'role' => 'member',
            ],
        ])->get(route('borrowing.show', $borrowing))
            ->assertOk()
            ->assertSee('Detail Peminjaman')
            ->assertSee('Silakan ambil barang di sekretariat HMIF.');
    }

    public function test_member_is_redirected_away_from_admin_dashboard(): void
    {
        $member = User::factory()->create([
            'name' => 'Ayu Pratiwi',
            'nim' => '121140001',
            'role' => 'member',
        ]);

        $this->actingAs($member)->withSession([
            'user' => [
                'name' => 'Ayu Pratiwi',
                'nim' => '121140001',
                'role' => 'member',
            ],
        ])->get(route('dashboard'))
            ->assertRedirect(route('member.dashboard'));
    }
}
