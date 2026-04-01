<?php

namespace App\Http\Controllers\Api\V1\ACL;

use App\Enums\Roles;
use App\Http\Controllers\Controller;
use App\Jobs\SyncUserPermissionsJob;
use App\Models\Module;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RolesController extends Controller
{
    public function index()
    {
        return Role::with('permissions')->get();
    }

    public function show(Role $role)
    {
        $modules = Module::with( 'permissions' )->get()->toArray();
        $rolePermission = $role->permissions()->pluck( 'id' )->toArray();
        foreach ( $modules as $key => $module ) {
            $enabled = true;
            if ( $role->id -1 == Roles::CUSTOMER && in_array($module['name'], ['Location', 'Country', 'State', 'City', 'Customer', 'Consignee', 'Report'])) {
                unset($modules[$key]);
                continue;
            }
            if (  in_array( $role->id -1, [ Roles::EMPLOYEE, Roles::ADMIN, Roles::LOCATION_ADMIN, Roles::LOCATION_VIEW_ADMIN, Roles::LOCATION_RESTRICTED_ADMIN ] ) && in_array($module['name'], ['Location', 'Country', 'State', 'City', 'Price']) ) {
                unset($modules[$key]);
                continue;
            }
            foreach ( $module[ 'permissions' ] as $pk => $permission ) {
                if (  in_array( $role->id -1, [ Roles::EMPLOYEE, Roles::ADMIN, Roles::LOCATION_ADMIN, Roles::LOCATION_VIEW_ADMIN, Roles::LOCATION_RESTRICTED_ADMIN ] ) && endsWith( $permission['identifier'], '.destroy' ) ) {
                    unset( $modules[ $key ][ 'permissions' ][ $pk ] );
                    continue;
                }
                if ( $role->id - 1 == Roles::CUSTOMER && ( endsWith( $permission['identifier'], '.store' ) || endsWith( $permission['identifier'], '.destroy' ) || endsWith( $permission['identifier'], '.update' ) || ( $module['name'] == 'Export' && endsWith( $permission['identifier'], '.index' ) ) ) ) {
                    unset( $modules[ $key ][ 'permissions' ][ $pk ] );
                    continue;
                }
                if ( ! in_array( $permission[ 'id' ], $rolePermission ) ) {
                    $enabled = false;
                }
                $modules[ $key ][ 'permissions' ][ $pk ][ 'has_access' ] = in_array( $permission[ 'id' ], $rolePermission );
            }
            $modules[ $key ]['permissions'] = array_values( $modules[ $key ] ['permissions']);
            $modules[ $key ][ 'enabled' ] = $enabled;
        }

        return [
            'role'        => $role,
            'permissions' => array_values( $modules ),
        ];

        /*return [
            'role' => $role,
            'permissions' => $role->permissions()->pluck('id'),
        ];*/
    }

    public function update(Role $role, Request $request)
    {
        $request->validate([
            'permissions' => 'required|array'
        ]);

        if (count($request->permissions) < 1) {
            $role->permissions()->detach();
            $role->forgetCachedPermissions();
        } else {
            $role->syncPermissions($request->permissions);
        }

        // Sync user permissions when role permissions updated
        SyncUserPermissionsJob::dispatch( $role->id - 1, $request->permissions );

        return response()->json([
            'user' => auth()->user(),
            'permissions' => auth()->user()->getAllPermissions()->pluck('name')->toArray()
        ]);
    }
}
