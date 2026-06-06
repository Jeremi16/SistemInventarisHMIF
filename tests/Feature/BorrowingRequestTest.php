<?php

namespace Tests\Feature;

use App\Models\Borrowing;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class BorrowingRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_can_submit_borrowing_request_and_get_whatsapp_confirmation(): void
    {
        $member = User::factory()->create([
            'name' => 'Ayu Pratiwi',
            'nim' => '121140001',
            'role' => 'member',
        ]);
        $admin = User::factory()->create(['role' => 'admin']);

        $item = Item::create([
            'name' => 'Laptop ASUS',
            'category' => 'Elektronik',
            'status' => 'available',
            'quantity' => 1,
            'condition' => 'good',
        ]);

        $this->actingAs($member)->withSession([
            'user' => [
                'name' => 'Ayu Pratiwi',
                'nim' => '121140001',
            ],
        ])
            ->get(route('borrowing.request', ['item_id' => $item->id]))
            ->assertOk()
            ->assertSee('Laptop ASUS')
            ->assertSee('Ayu Pratiwi')
            ->assertSee('121140001')
            ->assertSee('type="date"', false)
            ->assertDontSee('type="datetime-local"', false)
            ->assertSee('maxlength="100"', false);

        $response = $this->actingAs($member)->withSession([
            'user' => [
                'name' => 'Ayu Pratiwi',
                'nim' => '121140001',
            ],
        ])->post(route('borrowing.store'), [
            'item_id' => $item->id,
            'item_name' => $item->name,
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDay()->toDateString(),
            'purpose' => 'Digunakan untuk rapat kerja HMIF.',
            'terms_accepted' => '1',
        ]);

        $response
            ->assertRedirect(route('borrowing.request', ['item_id' => $item->id]))
            ->assertSessionHas('borrowing_success', function (array $success) {
                return $success['name'] === 'Ayu Pratiwi'
                    && $success['nim'] === '121140001'
                    && $success['item'] === 'Laptop ASUS'
                    && filled($success['borrowing_id'])
                    && str_contains(
                        urldecode($success['whatsapp_url']),
                        'Halo Sherizka, saya Ayu Pratiwi NIM 121140001 ingin konfirmasi peminjaman Laptop ASUS'
                    );
            });

        $this->assertDatabaseHas('borrowings', [
            'item_id' => $item->id,
            'item_name' => 'Laptop ASUS',
            'borrower_name' => 'Ayu Pratiwi',
            'borrower_nim' => '121140001',
            'status' => 'pending',
        ]);

        $borrowing = Borrowing::where('item_id', $item->id)->firstOrFail();
        $this->assertSame(now()->format('H:i:s'), $borrowing->start_date->format('H:i:s'));
        $this->assertSame($borrowing->start_date->format('H:i:s'), $borrowing->end_date->format('H:i:s'));

        $this->actingAs($admin)->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Aktivitas Peminjaman Terbaru')
            ->assertSee('Ayu Pratiwi')
            ->assertSee('Laptop ASUS')
            ->assertSee('Menunggu');

        $this->actingAs($admin)->get(route('incoming.index'))
            ->assertOk()
            ->assertSee('Mutasi Barang')
            ->assertSee('Barang Masuk')
            ->assertSee('Barang Keluar')
            ->assertSee('Catat Barang Masuk');

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
            ->assertSee('Menunggu');
    }

    public function test_admin_can_update_borrowing_status(): void
    {
        $admin = User::factory()->create([
            'name' => 'Sherizka',
            'email' => 'sherizka@hmif.itera.ac.id',
            'nim' => '121140002',
            'password' => Hash::make('secret-password'),
            'role' => 'admin',
        ]);

        $borrowing = Borrowing::create([
            'item_name' => 'Laptop ASUS',
            'borrower_name' => 'Ayu Pratiwi',
            'borrower_nim' => '121140001',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDay()->toDateString(),
            'purpose' => 'Digunakan untuk rapat kerja HMIF.',
            'status' => 'pending',
        ]);

        $this->actingAs($admin)->patch(route('borrowing.status.update', $borrowing), [
            'status' => 'approved',
            'admin_note' => 'Silakan ambil barang di sekretariat HMIF.',
        ])
            ->assertRedirect()
            ->assertSessionHas('borrowing_status_updated');

        $this->assertDatabaseHas('borrowings', [
            'id' => $borrowing->id,
            'status' => 'approved',
            'admin_note' => 'Silakan ambil barang di sekretariat HMIF.',
        ]);

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Laptop ASUS')
            ->assertSee('Disetujui')
            ->assertDontSee('Aksi Admin');

        $this->get(route('borrowing.index'))
            ->assertOk()
            ->assertSee('Silakan ambil barang di sekretariat HMIF.')
            ->assertSee('Disetujui')
            ->assertDontSee('value="pending"', false)
            ->assertDontSee('value="approved"', false)
            ->assertSee('value="borrowed"', false)
            ->assertSee('Diterima');
    }

    public function test_catalog_stock_updates_when_admin_changes_borrowing_status(): void
    {
        $admin = User::factory()->create([
            'name' => 'Sherizka',
            'email' => 'sherizka@hmif.itera.ac.id',
            'nim' => '121140002',
            'password' => Hash::make('secret-password'),
            'role' => 'admin',
        ]);

        $item = Item::create([
            'name' => 'Laptop ASUS',
            'category' => 'Elektronik',
            'status' => 'available',
            'quantity' => 1,
            'condition' => 'good',
            'location' => 'Lemari A',
        ]);

        $borrowing = Borrowing::create([
            'item_id' => $item->id,
            'item_name' => $item->name,
            'borrower_name' => 'Ayu Pratiwi',
            'borrower_nim' => '121140001',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDay()->toDateString(),
            'purpose' => 'Digunakan untuk rapat kerja HMIF.',
            'status' => 'pending',
        ]);

        $this->actingAs($admin)->patch(route('borrowing.status.update', $borrowing), [
            'status' => 'approved',
        ])->assertRedirect();

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'quantity' => 0,
            'status' => 'borrowed',
        ]);

        $this->get(route('catalog.index'))
            ->assertOk()
            ->assertSee('Laptop ASUS')
            ->assertSee('Tidak Tersedia');

        $this->actingAs($admin)->patch(route('borrowing.status.update', $borrowing->fresh()), [
            'status' => 'returned',
        ])->assertRedirect();

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'quantity' => 1,
            'status' => 'available',
        ]);

        $this->get(route('catalog.index'))
            ->assertOk()
            ->assertSee('Laptop ASUS')
            ->assertSee('Ajukan Pinjam');
    }

    public function test_member_cannot_update_borrowing_status(): void
    {
        $member = User::factory()->create([
            'name' => 'Ayu Pratiwi',
            'email' => 'ayu@hmif.itera.ac.id',
            'nim' => '121140001',
            'password' => Hash::make('secret-password'),
            'role' => 'member',
        ]);

        $borrowing = Borrowing::create([
            'item_name' => 'Laptop ASUS',
            'borrower_name' => 'Ayu Pratiwi',
            'borrower_nim' => '121140001',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDay()->toDateString(),
            'purpose' => 'Digunakan untuk rapat kerja HMIF.',
            'status' => 'pending',
        ]);

        $this->actingAs($member)->patch(route('borrowing.status.update', $borrowing), [
            'status' => 'approved',
            'admin_note' => 'Tidak boleh diubah member.',
        ])->assertForbidden();

        $this->assertDatabaseHas('borrowings', [
            'id' => $borrowing->id,
            'status' => 'pending',
            'admin_note' => null,
        ]);
    }

    public function test_member_cannot_open_other_member_borrowing_detail(): void
    {
        $member = User::factory()->create([
            'name' => 'Bima Santoso',
            'nim' => '121140099',
            'role' => 'member',
        ]);

        $borrowing = Borrowing::create([
            'item_name' => 'Laptop ASUS',
            'borrower_name' => 'Ayu Pratiwi',
            'borrower_nim' => '121140001',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDay()->toDateString(),
            'purpose' => 'Digunakan untuk rapat kerja HMIF.',
            'status' => 'pending',
        ]);

        $this->actingAs($member)->withSession([
            'user' => [
                'name' => 'Bima Santoso',
                'nim' => '121140099',
                'role' => 'member',
            ],
        ])->get(route('borrowing.show', $borrowing))
            ->assertForbidden();
    }

    public function test_borrowing_detail_limits_purpose_to_one_hundred_characters(): void
    {
        $member = User::factory()->create([
            'name' => 'Ayu Pratiwi',
            'nim' => '121140001',
            'role' => 'member',
        ]);

        $startDate = now()->setTime(9, 10, 11);
        $endDate = now()->addDay()->setTime(15, 20, 30);

        $borrowing = Borrowing::create([
            'item_name' => 'Laptop ASUS',
            'borrower_name' => 'Ayu Pratiwi',
            'borrower_nim' => '121140001',
            'start_date' => $startDate,
            'end_date' => $endDate,
            'purpose' => str_repeat('a', 120),
            'status' => 'pending',
        ]);

        $this->actingAs($member)->withSession([
            'user' => [
                'name' => 'Ayu Pratiwi',
                'nim' => '121140001',
                'role' => 'member',
            ],
        ])->get(route('borrowing.show', $borrowing))
            ->assertOk()
            ->assertSee($startDate->format('H:i:s'))
            ->assertSee($endDate->format('H:i:s'))
            ->assertSee(str_repeat('a', 100))
            ->assertDontSee(str_repeat('a', 101));

        $this->get(route('borrowing.index'))
            ->assertOk()
            ->assertSee($startDate->format('H:i:s'))
            ->assertSee($endDate->format('H:i:s'));
    }
}
