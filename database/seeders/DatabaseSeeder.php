<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(ItemSeeder::class);

        User::updateOrCreate([
            'email' => 'naruto@student.itera.ac.id',
        ], [
            'name' => 'Naruto Uzumaki',
            'nim' => '121140001',
            'password' => Hash::make('password'),
            'role' => 'member',
            'phone' => '081234567890',
            'batch' => '2021',
        ]);

        User::updateOrCreate([
            'email' => 'syahid@student.itera.ac.id',
        ], [
            'name' => 'Syahid',
            'nim' => '124140174',
            'password' => Hash::make('123'),
            'role' => 'admin',
            'phone' => '081234567890',
            'batch' => '2024',
        ]);
    }
}
