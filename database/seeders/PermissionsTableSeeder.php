<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        Permission::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        Permission::insert(array_merge(
//            $this->singleModules(),
            $this->resourcePermissionHelper( 'locations', 1 ),
            $this->resourcePermissionHelper( 'countries', 2 ),
            $this->resourcePermissionHelper( 'states', 3 ),
            $this->resourcePermissionHelper( 'cities', 4 ),
            $this->resourcePermissionHelper( 'customers', 5 ),
            $this->resourcePermissionHelper( 'consignees', 6 ),
            $this->resourcePermissionHelper( 'vehicles', 7 ),
            $this->resourcePermissionHelper( 'containers', 8 ),
            $this->resourcePermissionHelper( 'exports', 9 ),
            $this->resourcePermissionHelper( 'prices', 10 ),
//            $this->resourcePermissionHelper( 'reports', 11 ),
            $this->resourcePermissionHelper( 'invoices', 12 ),
        ));
    }

    private function resourcePermissionHelper ( $title, $moduleId = null )
    {
        return [
            [ 'name' => ucfirst($title).' Index', 'identifier' => "$title.index", 'module_id' => $moduleId, 'guard_name' => config( 'auth.defaults.guard' ), 'created_at' => Carbon::now(), 'updated_at' => Carbon::now() ],
            [ 'name' => ucfirst($title).' Detail', 'identifier' => "$title.view", 'module_id' => $moduleId, 'guard_name' => config( 'auth.defaults.guard' ), 'created_at' => Carbon::now(), 'updated_at' => Carbon::now() ],
            [ 'name' => ucfirst($title).' Create', 'identifier' => "$title.store", 'module_id' => $moduleId, 'guard_name' => config( 'auth.defaults.guard' ), 'created_at' => Carbon::now(), 'updated_at' => Carbon::now() ],
            [ 'name' => ucfirst($title).' Update', 'identifier' => "$title.update", 'module_id' => $moduleId, 'guard_name' => config( 'auth.defaults.guard' ), 'created_at' => Carbon::now(), 'updated_at' => Carbon::now() ],
            [ 'name' => ucfirst($title).' Delete', 'identifier' => "$title.destroy", 'module_id' => $moduleId, 'guard_name' => config( 'auth.defaults.guard' ), 'created_at' => Carbon::now(), 'updated_at' => Carbon::now() ],
        ];
    }

    private function singleModules() {
        return [
            ['name' => "view-dashboard", 'guard_name' => config('auth.defaults.guard'), 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => "view-vehicle-report", 'guard_name' => config('auth.defaults.guard'), 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => "view-container-report", 'guard_name' => config('auth.defaults.guard'), 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => "view-customer-report", 'guard_name' => config('auth.defaults.guard'), 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => "view-customer-record-report", 'guard_name' => config('auth.defaults.guard'), 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => "view-customer-invoice-record-report", 'guard_name' => config('auth.defaults.guard'), 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => "view-customer-title-status-report", 'guard_name' => config('auth.defaults.guard'), 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => "view-customer-management-report", 'guard_name' => config('auth.defaults.guard'), 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => "view-invoices", 'guard_name' => config('auth.defaults.guard'), 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => "locations-settings", 'guard_name' => config('auth.defaults.guard'), 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => "roles-settings", 'guard_name' => config('auth.defaults.guard'), 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]
        ];
    }
}
