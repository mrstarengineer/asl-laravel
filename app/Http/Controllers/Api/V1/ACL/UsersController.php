<?php

namespace App\Http\Controllers\Api\V1\ACL;

use App\Enums\Roles;
use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function index()
    {
        return User::with('permissions')->get();
    }

    public function show( User $user)
    {
        $modules = Module::with( 'permissions' )->get()->toArray();
        $rolePermission = $user->getAllPermissions()->pluck( 'id' )->toArray();
        foreach ( $modules as $key => $module ) {
            $enabled = true;
            if ( $user->role == Roles::CUSTOMER && in_array($module['name'], ['Location', 'Country', 'State', 'City', 'Customer', 'Consignee', 'Export', 'Report'])) {
                unset($modules[$key]);
                continue;
            }
            if (  in_array( $user->role -1, [ Roles::LOCATION_ADMIN, Roles::LOCATION_VIEW_ADMIN, Roles::LOCATION_RESTRICTED_ADMIN ] ) && in_array($module['name'], ['Location', 'Country', 'State', 'City']) ) {
                unset($modules[$key]);
                continue;
            }
            foreach ( $module[ 'permissions' ] as $pk => $permission ) {
                if ( $user->role == Roles::CUSTOMER && ( endsWith( $permission['identifier'], '.destroy' ) || endsWith( $permission['identifier'], '.update' ) ) ) {
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

        return response()->json([
            'user'        => $user->toArray(),
            'permissions' => array_values( $modules ),
        ]);
    }

    public function update(User $user, Request $request)
    {
        $request->validate([
            'permissions' => 'array'
        ]);

        if (count($request->permissions) < 1) {
            $user->permissions()->detach();
            $user->forgetCachedPermissions();
        } else {
            $user->syncPermissions($request->permissions);
        }

        $user->update( ['authentication_required' => 1] );

        return response()->json([
            'user'        => $user,
            'permissions' => $user->getAllPermissions()->toArray()
        ]);
    }
}
