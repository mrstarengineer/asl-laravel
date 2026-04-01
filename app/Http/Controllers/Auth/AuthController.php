<?php

namespace App\Http\Controllers\Auth;

use App\Enums\Roles;
use App\Http\Controllers\Controller;
use App\Models\Condition;
use App\Models\Feature;
use App\Models\Permission;
use App\Models\User;
use App\Presenters\UserPresenter;
use App\Services\Location\LocationService;
use App\Services\Vehicle\VehicleService;
use App\Services\Yard\YardService;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Validator;

class AuthController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware( 'auth:api', [ 'except' => [ 'login', 'register' ] ] );
    }

    /**
     * Get a JWT via given credentials.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login( Request $request )
    {
        $validator = Validator::make( $request->all(), [
            'email'    => 'required',
            'password' => 'required',
        ] );

        if ( $validator->fails() ) {
            return response()->json( [ 'status' => 422, 'errors' => $validator->errors() ], 422 );
        }

        $fieldType = filter_var( $request->email, FILTER_VALIDATE_EMAIL ) ? 'email' : 'username';

        $ttl = config( 'jwt.ttl' );
        $userData = [];
        if ( $request->get( 'source' ) === 'asl_phone_app' ) {
            config()->set( 'jwt.ttl', 60 * 24 * 365 );
            $ttl = 60 * 24 * 365;

            if ( $request->get( 'device_id_token' ) ) {
                $userData[ 'device_id_token' ] = $request->get( 'device_id_token' );
            }
        }

        if ( !$token = auth()->setTTL( $ttl )->attempt( [ $fieldType => $request->email, 'password' => $request->password ] ) ) {
            return response()->json( [ 'status' => 401, 'error' => 'Unauthorized' ], 401 );
        }

        $userData[ 'authentication_required' ] = 0;

        if ( $userData ) {
            auth()->user()->update( $userData );
        }

        return $this->createNewToken( $token );
    }

    /**
     * Register a User.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register( Request $request )
    {
        $validator = Validator::make( $request->all(), [
            'name'     => 'required|string|between:2,100',
            'email'    => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|confirmed|min:6',
        ] );

        if ( $validator->fails() ) {
            return response()->json( $validator->errors(), 400 );
        }

        $user = User::create( array_merge(
            $validator->validated(),
            [ 'password' => bcrypt( $request->password ) ]
        ) );

        return response()->json( [
            'message' => 'User successfully registered',
            'user'    => $user,
        ], 201 );
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json( [ 'message' => 'User successfully signed out' ] );
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->createNewToken( auth()->refresh() );
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function userProfile()
    {
        return response()->json( auth()->user() );
    }

    public function me()
    {
        $user = auth()->user();
        return response()->json( [
            'user'        => [
                'username' => $user->username,
                'email'    => $user->email,
                'role'     => trans( 'users.roles.' . $user->role ),
                'photo'    => $user->photo,
            ],
            'permissions' => $this->getPermissionList(),
        ] );
    }

    /**
     * Get the token array structure.
     *
     * @param string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken( $token )
    {
        $user = ( auth()->user() )->load( 'customer' );
        $data = [
            'status'       => 200,
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => config( 'jwt.ttl' ) * 60,
            'user'         => ( new UserPresenter( $user ) )->get(),
            'permissions'  => $this->getPermissionList(),
        ];

        if ( request()->get( 'with_additional_data' ) ) {
            $data[ 'vehicle_colors' ] = app( VehicleService::class )->getVehicleColors();
            $data[ 'vehicle_condition_items' ] = Condition::all( [ 'id', 'name' ] )->toArray();
            $data[ 'vehicle_checkbox_items' ] = Feature::all( [ 'id', 'name' ] )->toArray();
            $data[ 'locations' ] = app( LocationService::class )->all( [ 'status' => 1 ] );
            $data[ 'yards' ] = app( YardService::class )->all( [ 'status' => 1 ] );
        }

        return response()->json( $data );
    }

    private function getPermissionList()
    {
        $currentUser = auth()->user();
        $hasPermissions = $currentUser->getAllPermissions()->pluck( 'id' )->toArray();

        return Permission::all()->keyBy( 'identifier', )->map( function ( $item ) use ( $hasPermissions, $currentUser ) {
            return $currentUser->role == Roles::MASTER_ADMIN || in_array( $item->id, $hasPermissions );
        } );
    }
}
