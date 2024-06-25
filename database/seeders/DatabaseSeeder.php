<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            UserSeeder::class,
            ClienteSeeder::class,
            ProdutoSeeder::class,
            VendaSeeder::class,
            ItensVendaSeeder::class,
        ]);
    }
}
