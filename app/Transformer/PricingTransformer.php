<?php

namespace App\Transformer;

use App\Enums\VisibilityStatus;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class PricingTransformer extends \Nahid\Presento\Transformer
{

    public function getStatusNameProperty( $value )
    {
        return $value == VisibilityStatus::ACTIVE ? 'Active' : 'Inactive';
    }

    public function getPricingTypeNameProperty( $value )
    {
        return Arr::get( trans( 'pricing.pricing_type' ), $value, '' );
    }

    public function getCreatedAtProperty( $value )
    {
        return date( 'Y-m-d H:i:s', strtotime( $value ) );
    }

    public function getUpdatedAtProperty( $value )
    {
        return date( 'Y-m-d H:i:s', strtotime( $value ) );
    }

    public function getFileUrlProperty( $value )
    {
        return ( !empty( $value ) && filter_var( $value, FILTER_VALIDATE_URL ) === false ) ? Storage::url( $value ) : $value;
    }

    public function getMonthNameProperty( $value )
    {
        return date( 'F Y', strtotime( $value ) );
    }

}

