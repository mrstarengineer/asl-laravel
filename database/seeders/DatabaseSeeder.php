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
        // Every seeder in this folder is listed here. Uncomment a line to run it with `php artisan db:seed`.
        $this->call([
            ReferenceDataSeeder::class,     // roles, modules, permissions, locations, countries, states, cities, conditions, features, colors
            LocalAdminSeeder::class,        // admin / password, local environment only

//            LocationTableSeeder::class,
//            CountrySeeder::class,
//            StateSeeder::class,
//            CitySeeder::class,
//            RolesTableSeeder::class,
//            ModuleSeeder::class,
//            PermissionsTableSeeder::class,  // truncates the permissions table first
//            StreamshipLineSeeder::class,
//            UserSeeder::class,              // real user accounts
        ]);
    }
}
