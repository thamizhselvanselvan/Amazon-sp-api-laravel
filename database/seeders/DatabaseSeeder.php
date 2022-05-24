<?php

namespace Database\Seeders;

use Database\Seeders\Inventory\INUserSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        
        $this->call([
             UserSeeder::class,
             Catalog_Manager::class,
             Boe_Account::class,
             INUserSeeder::class,
             Seller_Management::class,
             CountryStateCitySeeder::class,
        ]);
    }
}
