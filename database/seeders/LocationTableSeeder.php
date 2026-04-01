<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class LocationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('locations')->insert([
            ['name' => 'LA'],
            ['name' => 'GA'],
            ['name' => 'NY'],
            ['name' => 'TX'],
            ['name' => 'BALTIMORE'],
            ['name' => 'NEWJ-2'],
            ['name' => 'TEXAS'],
            ['name' => 'NJ'],
            ['name' => 'Test'],
            ['name' => 'test2'],
        ]);
    }
}
