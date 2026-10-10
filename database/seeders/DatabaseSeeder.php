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
        // Enough to run the project on an empty database (see docker/php/entrypoint.sh).
        // The older seeders in this folder are historical and no longer match production.
        $this->call([
            ReferenceDataSeeder::class,
            LocalAdminSeeder::class,
        ]);
    }
}
