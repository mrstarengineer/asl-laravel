<?php

namespace App\Transformer;

use App\Enums\Roles;
use App\Models\Customer;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class ClaimTransformer extends \Nahid\Presento\Transformer
{
    public function getCustomerPhotosProperty ( $photos )
    {
        return collect( $photos )->reject( function ( $item ) {
            return ! Storage::exists( $item[ 'image' ] );
        })->map( function ( $item ) {
            return [
                'id'   => $item[ 'id' ],
                'name' => basename( filter_var( $item[ 'image' ], FILTER_VALIDATE_URL ) === false ? Storage::url( $item[ 'image' ] ) : $item[ 'image' ] ),
                'url'  => filter_var( $item[ 'image' ], FILTER_VALIDATE_URL ) === false ? Storage::url( $item[ 'image' ] ) : $item[ 'image' ],
                'type' => $item[ 'type' ],
                'size' => $item[ 'size' ],
            ];
        } )->values()->all();

//        return collect( $photos )->map( function ( $item ) {
//            return [
//                'id'   => $item[ 'id' ],
//                'name' => basename( filter_var( $item[ 'image' ], FILTER_VALIDATE_URL ) === false ? Storage::url( $item[ 'image' ] ) : $item[ 'image' ] ),
//                'url'  => filter_var( $item[ 'image' ], FILTER_VALIDATE_URL ) === false ? Storage::url( $item[ 'image' ] ) : $item[ 'image' ],
//                'type' => $item[ 'type' ],
//                'size' => $item[ 'size' ],
//            ];
//        } )->values()->all();
    }

    public function getAdminPhotosProperty ( $photos )
    {
        return collect( $photos )->reject( function ( $item ) {
            return ! Storage::exists( $item[ 'image' ] );
        })->map( function ( $item ) {
            return [
                'id'   => $item[ 'id' ],
                'name' => basename( filter_var( $item[ 'image' ], FILTER_VALIDATE_URL ) === false ? Storage::url( $item[ 'image' ] ) : $item[ 'image' ] ),
                'url'  => filter_var( $item[ 'image' ], FILTER_VALIDATE_URL ) === false ? Storage::url( $item[ 'image' ] ) : $item[ 'image' ],
                'type' => $item[ 'type' ],
                'size' => $item[ 'size' ],
            ];
        } )->values()->all();

//        return collect( $photos )->map( function ( $item ) {
//            return [
//                'id'   => $item[ 'id' ],
//                'name' => basename( filter_var( $item[ 'image' ], FILTER_VALIDATE_URL ) === false ? Storage::url( $item[ 'image' ] ) : $item[ 'image' ] ),
//                'url'  => filter_var( $item[ 'image' ], FILTER_VALIDATE_URL ) === false ? Storage::url( $item[ 'image' ] ) : $item[ 'image' ],
//                'type' => $item[ 'type' ],
//                'size' => $item[ 'size' ],
//            ];
//        } )->values()->all();
    }

    public function getStatusNameProperty ( $value )
    {
        return Arr::get( trans( 'vehicle_claim.claim_statuses' ), $value, '' );
    }
}

