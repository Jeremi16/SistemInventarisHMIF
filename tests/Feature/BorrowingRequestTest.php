<?php

namespace Tests\Feature;

use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BorrowingRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_can_submit_borrowing_request_and_get_whatsapp_confirmation(): void
    {
        $item = Item::create([
            'name' => 'Laptop ASUS',
            'category' => 'Elektronik',
            'status' => 'available',
            'quantity' => 1,
            'condition' => 'good',
        ]);

        $this->withSession([
            'user' => [
                'name' => 'Ayu Pratiwi',
                'nim' => '121140001',
            ],
        ])
            ->get(route('borrowing.request', ['item_id' => $item->id]))
            ->assertOk()
            ->assertSee('Laptop ASUS')
            ->assertSee('Ayu Pratiwi')
            ->assertSee('121140001');

        $response = $this->withSession([
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
                    && str_contains(
                        urldecode($success['whatsapp_url']),
                        'Halo Sherizka, saya Ayu Pratiwi NIM 121140001 ingin konfirmasi peminjaman Laptop ASUS'
                    );
            });
    }
}
