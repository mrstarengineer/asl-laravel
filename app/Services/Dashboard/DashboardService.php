<?php


namespace App\Services\Dashboard;


use App\Enums\Roles;
use App\Enums\VehicleStatus;
use App\Enums\VisibilityStatus;
use App\Models\Vehicle;
use App\Models\User;
use App\Services\BaseService;
use App\Services\User\UserService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;


class DashboardService extends BaseService
{
    public function vehicleCounts( $filters = [] ): array
    {

        $data = [];
//        $data[] = ['status' => 0, 'label' => 'All Vehicles', 'total' => $this->getCounts($filters)];
        foreach ( config( 'vehicle.statuses' ) as $item ) {
            if ( optional( auth()->user() )->role === Roles::LOCATION_RESTRICTED_ADMIN && !in_array( $item[ 'status' ], [ VehicleStatus::ON_HAND, VehicleStatus::ON_THE_WAY ] ) ) {
                continue;
            }
            $dataArr = array_merge( $item, [ 'total' => $this->getCounts( $filters, $item[ 'status' ] ) ] );
            if ( Arr::get( $filters, 'include_inhouse_inventories' ) && $item[ 'label' ] === 'All Vehicles' ) {
                $dataArr = array_merge( $dataArr, [ 'label' => 'All Inventory', 'status' => '1,3', 'total' => $this->getCounts( array_merge( $filters, [ 'statuses' => [ VehicleStatus::ON_HAND, VehicleStatus::ON_THE_WAY ] ] ) ) ] );
            }
            $dataArr = array_merge( $dataArr, [ 'logo' => url( $item[ 'logo' ] ) ] );

            array_push( $data, $dataArr );
        }

        return $data;
    }

    private function getCounts( $filters = [], $status = null ): int
    {
        $query = Vehicle::join( 'towing_requests', 'vehicles.towing_request_id', '=', 'towing_requests.id' );

        if ( !empty( $filters[ 'user_id' ] ) ) {
            $query->where( 'customer_user_id', $filters[ 'user_id' ] );
        }

        if ( !empty( $filters[ 'location_id' ] ) ) {
            $query->where( 'location_id', $filters[ 'location_id' ] );
        }

        if ( in_array( optional( auth()->user() )->role, [ Roles::LOCATION_ADMIN, Roles::LOCATION_VIEW_ADMIN, Roles::LOCATION_RESTRICTED_ADMIN ] ) ) {
            $query->whereIn( 'location_id', auth()->user()->locations );
        }

        if ( optional( auth()->user() )->customers ) {
            $query->whereHas( 'customer', function ( $q ) {
                $q->whereIn( 'legacy_customer_id', auth()->user()->customers );
            } );
        }

        if ( $status != '' && $status != null ) {
            $query->where( 'status', $status );
        }

        if ( !empty( $filters[ 'statuses' ] ) ) {
            $query->whereIn( 'status', $filters[ 'statuses' ] );
        }

//        if ( in_array( $status, [ VehicleStatus::ARRIVED, VehicleStatus::SHIPPED ] ) ) {
//            $query->whereHas( 'export' );
//        }

        return $query->count();
    }

    /* Need to refactor - move to user service */
    public function userInfo( $filters = [] )
    {
        $user = null;
        if ( !empty( $filters[ 'user_id' ] ) ) {
            $user = app( UserService::class )->getById( $filters[ 'user_id' ] );
            $user = [
                'username' => $user->username,
                'email'    => $user->email,
                'role'     => trans( 'users.roles.' . $user->role ),
                'photo'    => $user->photo,
            ];
        }

        return $user;
    }
}
