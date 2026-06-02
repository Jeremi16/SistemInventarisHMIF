<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_new_item_form_and_create_item(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->get(route('inventory.create'))
            ->assertOk()
            ->assertSee('Barang Baru')
            ->assertSee('Simpan Barang');

        $response = $this->post(route('inventory.store'), [
            'name' => 'Proyektor Epson EB-X400',
            'category' => 'Elektronik',
            'quantity' => 2,
            'status' => 'available',
            'condition' => 'good',
            'location' => 'Lemari A',
            'description' => 'Proyektor untuk kegiatan HMIF.',
        ]);

        $item = Item::where('name', 'Proyektor Epson EB-X400')->firstOrFail();

        $response
            ->assertRedirect(route('inventory.show', $item))
            ->assertSessionHas('item_created');

        $this->assertDatabaseHas('items', [
            'name' => 'Proyektor Epson EB-X400',
            'category' => 'Elektronik',
            'quantity' => 2,
            'status' => 'available',
            'condition' => 'good',
            'location' => 'Lemari A',
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
}
