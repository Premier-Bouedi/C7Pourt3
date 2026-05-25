<?php

namespace Database\Seeders;

use App\Support\CatalogData;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        Artisan::call('catalog:sync', ['--images' => true]);
    }
}
