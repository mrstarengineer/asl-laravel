<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run ()
    {
        DB::table( 'modules', )->insert( [
            [ 'name' => 'Location', ],
            [ 'name' => 'Country', ],
            [ 'name' => 'State', ],
            [ 'name' => 'City', ],
            [ 'name' => 'Customer', ],
            [ 'name' => 'Consignee', ],
            [ 'name' => 'Vehicle', ],
            [ 'name' => 'Container', ],
            [ 'name' => 'Export', ],
            [ 'name' => 'Price', ],
            [ 'name' => 'Report', ],
            [ 'name' => 'Invoice', ],
        ] );
    }
}
