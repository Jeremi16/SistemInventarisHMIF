<?php

namespace Tests\Feature;

use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_new_item_form_and_create_item(): void
    {
        $this->get(route('inventory.create'))
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
        $item = Item::create([
            'name' => 'Laptop ASUS',
            'category' => 'Elektronik',
            'status' => 'available',
            'quantity' => 1,
            'condition' => 'good',
            'location' => 'Lemari A',
            'description' => 'Laptop untuk kegiatan HMIF.',
        ]);

        $this->get(route('inventory.index'))
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
