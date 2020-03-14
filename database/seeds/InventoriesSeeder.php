<?php

use Illuminate\Database\Seeder;

class InventoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('inventories')->insert([
            'name' => 'Alcohol 100ml',
            'description' => 'Presentación pequeña',
            'price' => 1.99,
            'stock' => 500,
            'type' => 'Producto final'
        ]);
        DB::table('inventories')->insert([
            'name' => 'Alcohol 250ml',
            'description' => 'Presentación mediana',
            'price' => 2.99,
            'stock' => 300,
            'type' => 'Producto final'
        ]);
        DB::table('inventories')->insert([
            'name' => 'Alcohol 800ml',
            'description' => 'Presentación grande',
            'price' => 4.99,
            'stock' => 100,
            'type' => 'Producto final'
        ]);
        DB::table('inventories')->insert([
            'name' => 'Alcohol 2lt',
            'description' => 'Presentación jumbo',
            'price' => 10.00,
            'stock' => 50,
            'type' => 'Producto final'
        ]);
    }
}
