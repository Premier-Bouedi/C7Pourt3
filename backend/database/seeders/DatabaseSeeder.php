<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin C7Pourt3',
            'email' => 'admin@c7pourt3.com',
            'role' => 'admin',
        ]);

        $this->call(CatalogSeeder::class);
    }
}
