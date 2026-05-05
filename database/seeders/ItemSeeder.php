<?php

namespace Database\Seeders;

use App\Models\Item;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'name' => 'Laptop ASUS',
                'category' => 'Elektronik',
                'status' => 'available',
                'quantity' => 2,
                'condition' => 'good',
                'location' => 'Lemari A',
                'description' => 'Laptop ASUS untuk keperluan kegiatan HMIF.',
            ],
            [
                'name' => 'Proyektor Epson',
                'category' => 'Event Gear',
                'status' => 'borrowed',
                'quantity' => 1,
                'condition' => 'good',
                'location' => 'Lemari B',
                'description' => 'Proyektor Epson untuk presentasi dan acara.',
            ],
            [
                'name' => 'Speaker JBL',
                'category' => 'Event Gear',
                'status' => 'available',
                'quantity' => 1,
                'condition' => 'good',
                'location' => 'Lemari B',
                'description' => 'Speaker portabel untuk acara outdoor.',
            ],
            [
                'name' => 'Kabel HDMI',
                'category' => 'Aksesoris',
                'status' => 'available',
                'quantity' => 5,
                'condition' => 'good',
                'location' => 'Laci 1',
                'description' => 'Kabel HDMI 3m untuk koneksi display.',
            ],
            [
                'name' => 'Tripod',
                'category' => 'Event Gear',
                'status' => 'available',
                'quantity' => 2,
                'condition' => 'good',
                'location' => 'Lemari C',
                'description' => 'Tripod kamera untuk dokumentasi acara.',
            ],
            [
                'name' => 'Extension Cord',
                'category' => 'Aksesoris',
                'status' => 'borrowed',
                'quantity' => 3,
                'condition' => 'fair',
                'location' => 'Laci 2',
                'description' => 'Kabel extension 5m untuk kebutuhan listrik.',
            ],
            [
                'name' => 'Microphone Wireless',
                'category' => 'Event Gear',
                'status' => 'available',
                'quantity' => 2,
                'condition' => 'good',
                'location' => 'Lemari B',
                'description' => 'Microphone wireless untuk MC dan pembicara.',
            ],
            [
                'name' => 'Whiteboard',
                'category' => 'Perlengkapan',
                'status' => 'available',
                'quantity' => 1,
                'condition' => 'good',
                'location' => 'Ruang Rapat',
                'description' => 'Whiteboard 120x90cm untuk meeting.',
            ],
            [
                'name' => 'Kertas HVS',
                'category' => 'Perlengkapan',
                'status' => 'available',
                'quantity' => 10,
                'condition' => 'good',
                'location' => 'Lemari D',
                'description' => 'Kertas HVS A4 untuk kebutuhan cetak.',
            ],
            [
                'name' => 'Camera DSLR Canon',
                'category' => 'Elektronik',
                'status' => 'maintenance',
                'quantity' => 1,
                'condition' => 'fair',
                'location' => 'Lemari A',
                'description' => 'Kamera DSLR untuk dokumentasi kegiatan.',
            ],
        ];

        foreach ($items as $item) {
            Item::create($item);
        }
    }
}
