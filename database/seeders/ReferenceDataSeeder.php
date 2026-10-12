<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Lookup data the application cannot run without: roles, modules, permissions, locations,
 * countries/states/cities, vehicle conditions, features and colors.
 *
 * The rows are in database/seeders/data/<table>.json and keep their production ids, because
 * the code refers to some of them by id (location 16 is China, permissions.module_id, role ids).
 * Rows that already exist are left alone, so this is safe to run more than once.
 */
class ReferenceDataSeeder extends Seeder
{
    /** In insert order. */
    private $tables = [
        'roles',
        'modules',
        'permissions',
        'role_has_permissions',
        'locations',
        'countries',
        'states',
        'cities',
        'conditions',
        'features',
        'vehicle_colors',
    ];

    public function run()
    {
        foreach ( $this->tables as $table ) {
            $rows = json_decode( file_get_contents( __DIR__ . "/data/$table.json" ), true );

            foreach ( array_chunk( $rows, 200 ) as $chunk ) {
                DB::table( $table )->insertOrIgnore( $chunk );
            }
        }

        app( \Spatie\Permission\PermissionRegistrar::class )->forgetCachedPermissions();
    }
}
B
