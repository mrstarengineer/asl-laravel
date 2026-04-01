<?php

namespace App\Transformer;

use App\Enums\Roles;
use Carbon\Carbon;
use Illuminate\Support\Arr;

class NoteTransformer extends \Nahid\Presento\Transformer
{
    public function getCreatedByProperty( $value )
    {
        if ( !empty( $value[ 'customer' ] ) ) {
            return Arr::get( $value, 'customer.customer_name' );
        }

        return Arr::get( $value, 'username' );
    }

    public function getCreatedAtProperty( $value )
    {
        return Carbon::parse( $value )->format( 'Y-m-d' );
    }

    public function getAdminViewProperty( $value )
    {
        return $value == 1 ? 'admin_unread' : 'admin_read';
    }

    public function getCustViewProperty( $value )
    {
        return $value == 1 ? 'cust_unread' : 'cust_read';
    }

}

