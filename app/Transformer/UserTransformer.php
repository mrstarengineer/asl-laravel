<?php

namespace App\Transformer;

use App\Enums\Roles;
use App\Models\Customer;
use App\Models\Location;

class UserTransformer extends \Nahid\Presento\Transformer
{
    public function getStatusProperty ( $value )
    {
        switch ( $value ) {
            case 0 :
                $val = 'Inactive';
                break;
            case 1 :
                $val = 'Active';
                break;
            default:
                $val = '';
                break;
        }

        return $val;
    }

    public function getRoleNameProperty ( $value )
    {
        switch ( $value ) {
            case Roles::CUSTOMER :
                $data = $this->getData();
                $customer = Customer::where( 'user_id', $data[ 'id' ] )->first();
//                $val = $customer ? $customer->company_name : 'Customer';
                $val = 'Customer';
                break;
            case Roles::MASTER_ADMIN :
                $val = 'Master Admin';
                break;
            case Roles::SUPER_ADMIN :
                $val = 'Super Admin';
                break;
            case Roles::LOCATION_ADMIN :
                $val = 'Location Admin';
                break;
            case Roles::LOCATION_VIEW_ADMIN :
                $val = 'Location View Admin';
                break;
            case Roles::LOCATION_RESTRICTED_ADMIN :
                $val = 'Location Restricted Admin';
                break;
            case Roles::EMPLOYEE :
                $val = 'Employee';
                break;
            case Roles::ACCOUNT :
                $val = 'Account';
                break;
            case Roles::ADMIN :
                $val = 'Admin';
                break;
            default:
                $val = 'Customer';
                break;
        }

        return $val;
    }

    public function getLocationNamesProperty ( $locationIds )
    {
        $locationName = '';

        if($locationIds) {
            $locationList = Location::whereIn( 'id', $locationIds )->get();
            foreach ( $locationList as $location ) {
                $locationName .= ! empty( $locationName ) ? ', ' : '' . $location->name;
            }
        }

        return $locationName;
    }


}

