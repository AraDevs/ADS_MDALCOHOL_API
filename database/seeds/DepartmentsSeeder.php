<?php

use Illuminate\Database\Seeder;

class DepartmentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    
    public function run()
    {
        $departments = array('Ahuachapán', 'Santa Ana', 'Sonsonate', 'La Libertad', 'Chalatenango',
                             'San Salvador', 'Cuscatlán', 'La Paz', 'Cabañas', 'San Vicente',
                             'Usulután', 'Morazán', 'San Miguel', 'La Unión');

        foreach($departments as $department) {
            DB::table('departments')->insert([
                'name' => $department
            ]);
        }
        
    }
}
