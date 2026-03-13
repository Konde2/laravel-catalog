<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CatalogSeeder extends Seeder
{
    /**
     * Запустить все сидеры каталога.
     */
    public function run(): void
    {
        $this->call([
            GroupSeeder::class,
            ProductSeeder::class,
            PriceSeeder::class,
        ]);
    }
}
