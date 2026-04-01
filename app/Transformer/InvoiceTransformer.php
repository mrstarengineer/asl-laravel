<?php

namespace App\Transformer;

use App\Enums\InvoiceStatus;
use App\Enums\Roles;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class InvoiceTransformer extends \Nahid\Presento\Transformer
{
    public function getDateProperty ( $value ): string
    {
        return Carbon::parse( $value )->format( 'Y-m-d' );
    }

    public function getBalanceProperty ( $value ): string
    {
        $data = $this->getData();

        return number_format( $data['total_amount'] - $data['adjustment_damaged'] - $data['adjustment_storage'] - $data['adjustment_discount'] - $data['adjustment_other'] - $data['paid_amount'], 2 );
    }

    public function getClearanceInvoiceProperty ( $value )
    {
        if ( empty( $value ) ) {
            return null;
        }

        if ( filter_var( $value, FILTER_VALIDATE_URL ) === false ) {
            return Storage::url( $value );
        }

        return $value;
    }

    public function getClearanceInvoiceItemProperty ( $value )
    {
        if ( empty( $value ) ) {
            return null;
        }

        return [
            'id'   => 0,
            'name' => basename( $value ),
            'url'   => filter_var( $value, FILTER_VALIDATE_URL ) === false ? Storage::url( $value ) : $value,
            'type'  => pathinfo($value, PATHINFO_EXTENSION),
            'size'  => null,
        ];
    }

    public function getUploadInvoiceProperty ( $value )
    {
        if ( empty( $value ) ) {
            return null;
        }

        if ( filter_var( $value, FILTER_VALIDATE_URL ) === false ) {
            return Storage::url( $value );
        }

        return $value;
    }

    public function getUploadInvoiceItemProperty ( $value )
    {
        if ( empty( $value ) ) {
            return null;
        }

        return [
            'id'   => 0,
            'name' => basename( $value ),
            'url'  => filter_var( $value, FILTER_VALIDATE_URL ) === false ? Storage::url( $value ) : $value,
            'type' => pathinfo($value, PATHINFO_EXTENSION),
            'size' => null,
        ];
    }

    public function getUsaInvoiceProperty ( $value )
    {
        if ( empty( $value ) || !Storage::exists($value) ) {
            return null;
        }

        if ( filter_var( $value, FILTER_VALIDATE_URL ) === false ) {
            return Storage::url( $value );
        }

        return $value;
    }

    public function getStatusProperty ()
    {
        $data = $this->getData();
        $balance = number_format( $data['total_amount'] - $data['adjustment_damaged'] - $data['adjustment_storage'] - $data['adjustment_discount'] - $data['adjustment_other'] - $data['paid_amount'], 2 );

        if ( $balance == $data['total_amount'] ) {
            return InvoiceStatus::UNPAID;
        } elseif ( $balance == 0.00 ) {
            return InvoiceStatus::PAID;
        }

        return InvoiceStatus::PARTIALLY_PAID;
    }

    public function getStatusNameProperty ()
    {
        $data = $this->getData();
        $balance = number_format( $data['total_amount'] - $data['adjustment_damaged'] - $data['adjustment_storage'] - $data['adjustment_discount'] - $data['adjustment_other'] - $data['paid_amount'], 2 );

        if ( (int) $data[ 'paid_amount' ] == 0 ) {
            return 'Unpaid';
        } elseif ( $balance <= 0.00 ) {
            return 'Paid';
        }

        return 'Partially Paid';
    }

    public function getDocumentsProperty ( $values )
    {
        $count = 0;
        return collect( $values )->reject( function ( $item ) {
            return ! Storage::exists( $item[ 'name' ] );
        })->map( function ( $item ) use ( &$count ) {
            return [
                'id'   => $item['id'],
                'label' => 'Doc-' . ++$count,
                'url'   => filter_var( $item[ 'name' ], FILTER_VALIDATE_URL ) === false ? Storage::url( $item[ 'name' ] ) : $item[ 'name' ],
                'type'  => $item[ 'type' ],
                'size'  => $item[ 'size' ],
            ];
        } )->values()->all();

//        return collect( $values )->map( function ( $item ) use ( &$count ) {
//            return [
//                'id'   => $item['id'],
//                'label' => 'Doc-' . ++$count,
//                'url'   => filter_var( $item[ 'name' ], FILTER_VALIDATE_URL ) === false ? Storage::url( $item[ 'name' ] ) : $item[ 'name' ],
//                'type'  => $item[ 'type' ],
//                'size'  => $item[ 'size' ],
//            ];
//        } )->values()->all();
    }
}

