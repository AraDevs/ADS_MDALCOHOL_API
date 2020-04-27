<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call('DepartmentsSeeder');
        $this->call('MunicipalitiesSeeder');
        $this->call('InventoriesSeeder');
        $this->call('UserSeeder');
    }
}
