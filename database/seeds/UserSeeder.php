<?php

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'username' => 'root',
            'name' => 'Marcela Monge',
            'password' => '$2y$10$ZbipvcVgydqq9VH88idWcO8FsKz1F0Crlre2XEq/5yPL2tqOeJ/PG',
            'user_type' => 'Administración',
            'state' => 1
        ]);
    }
}
