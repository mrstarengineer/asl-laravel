<?php

namespace Database\Seeders;

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
//            LocationTableSeeder::class,
//            CountrySeeder::class,
//            StateSeeder::class,
//            CitySeeder::class,
//            RolesTableSeeder::class,
//            PermissionsTableSeeder::class,
//            ModuleSeeder::class,
            UserSeeder::class,
        ]);
    }
}
