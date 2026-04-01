<?php

namespace App\Transformer;

use App\Enums\Roles;
use App\Enums\VisibilityStatus;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class CustomerTransformer extends \Nahid\Presento\Transformer
{
	public function getLoadingTypeTitleProperty($value)
	{
		switch ($value) {
			case 1 :
				$val = 'FULL';
				break;
			case 2 :
				$val = 'MIX';
                break;
            default:
                $val = '';
                break;
        }

        return $val;
    }

    public function getStatusNameProperty ( $value )
    {
        return $value == VisibilityStatus::ACTIVE ? 'Active' : 'Inactive';
    }
    public function getInactiveAtProperty ( $value )
    {
        return Carbon::parse($value)
            ->timezone('Asia/Dubai')
            ->format('M d, Y h:i A');
    }

    public function getRoleNameProperty ( $value )
    {
        switch ( $value ) {
            case Roles::MASTER_ADMIN :
                $val = 'Master Admin';
                break;
            case Roles::SUPER_ADMIN :
                $val = 'Super Admin';
                break;
            case Roles::LOCATION_ADMIN :
                $val = 'Admin';
                break;
            default:
                $val = 'Customer';
                break;
        }

        return $val;
    }

    public function getCustomerDocumentsProperty ( $documents )
    {
        return collect( $documents )->map( function ( $item ) {
            return [
                'id'   => $item[ 'id' ],
                'name' => basename( $item[ 'file' ] ),
                'url'  => filter_var( $item[ 'file' ], FILTER_VALIDATE_URL ) === false ? Storage::url( $item[ 'file' ] ) : $item[ 'file' ],
                'type' => $item[ 'type' ],
                'size' => $item[ 'size' ],
            ];
        } );
    }

    public function getCreatedAtProperty ( $value )
    {
        return Carbon::parse($value)
            ->timezone('Asia/Dubai')
            ->format('M d, Y h:i A');
    }


}

