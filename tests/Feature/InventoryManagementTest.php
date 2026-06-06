<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_cannot_create_new_item_from_inventory(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->get(route('inventory.index'))
            ->assertOk()
            ->assertDontSee('Barang Baru');

        $this->actingAs($admin)->get('/inventory/create')
            ->assertNotFound();

        $this->actingAs($admin)->post('/inventory', [
            'name' => 'Proyektor Epson EB-X400',
            'category' => 'Elektronik',
            'quantity' => 2,
            'status' => 'available',
            'condition' => 'good',
            'location' => 'Lemari A',
            'description' => 'Proyektor untuk kegiatan HMIF.',
        ])->assertStatus(405);

        $this->assertDatabaseMissing('items', [
            'name' => 'Proyektor Epson EB-X400',
        ]);
    }

    public function test_inventory_detail_link_is_available(): void
    {
        $member = User::factory()->create(['role' => 'member']);

        $item = Item::create([
            'name' => 'Laptop ASUS',
            'category' => 'Elektronik',
            'status' => 'available',
            'quantity' => 1,
            'condition' => 'good',
            'location' => 'Lemari A',
            'description' => 'Laptop untuk kegiatan HMIF.',
        ]);

        $this->actingAs($member)->get(route('inventory.index'))
            ->assertOk()
            ->assertSee(route('inventory.show', $item), false)
            ->assertSee('Lihat Detail');

        $this->get(route('inventory.show', $item))
            ->assertOk()
            ->assertSee('Detail Inventaris')
            ->assertSee('Laptop ASUS')
            ->assertSee('Laptop untuk kegiatan HMIF.');
    }

    public function test_admin_can_record_incoming_item_with_typed_item_name(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->get(route('incoming.create'))
            ->assertOk()
            ->assertSee('name="item_name"', false)
            ->assertSee('Masukkan nama barang')
            ->assertDontSee('Pilih barang');

        $response = $this->post(route('incoming.store'), [
            'item_name' => 'Kabel HDMI',
            'source' => 'pembelian',
            'date' => now()->toDateString(),
            'quantity' => 3,
            'notes' => 'Untuk aula',
        ]);

        $item = Item::where('name', 'Kabel HDMI')->firstOrFail();

        $response
            ->assertRedirect(route('incoming.index'))
            ->assertSessionHas('incoming_created');

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'name' => 'Kabel HDMI',
            'category' => 'Lainnya',
            'quantity' => 3,
            'status' => 'available',
            'condition' => 'good',
        ]);

        $this->assertDatabaseHas('item_incomings', [
            'item_id' => $item->id,
            'source' => 'pembelian',
            'quantity' => 3,
            'notes' => 'Untuk aula',
        ]);
    }
}
