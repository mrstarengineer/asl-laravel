<?php

namespace Database\Seeders;

use App\Enums\Roles;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * One master admin for local development: admin / password.
 * Does nothing outside the local environment.
 */
class LocalAdminSeeder extends Seeder
{
    public function run()
    {
        if ( !app()->environment( 'local' ) ) {
            return;
        }

        if ( User::where( 'username', 'admin' )->orWhere( 'email', 'admin@asl.local' )->exists() ) {
            return;
        }

        User::create( [
            'username' => 'admin',
            'email'    => 'admin@asl.local',
            'password' => bcrypt( 'password' ),
            'role'     => Roles::MASTER_ADMIN,
            'status'   => 1,
        ] );
    }
}
