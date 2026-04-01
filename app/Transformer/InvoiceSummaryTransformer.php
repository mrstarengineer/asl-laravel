<?php

namespace App\Transformer;

use App\Enums\Roles;
use Illuminate\Support\Carbon;

class InvoiceSummaryTransformer extends \Nahid\Presento\Transformer
{
    public function getTotalAmountProperty ( $invoices ): string
    {
        return number_format( collect( $invoices )->sum( 'total_amount' ), 2 );
    }

    public function getPaidAmountProperty ( $invoices ): string
    {
        return number_format( collect( $invoices )->sum( 'paid_amount' ), 2 );
    }

    public function getDiscountProperty ( $invoices ): string
    {
        return number_format( collect( $invoices )->sum( 'discount' ), 2 );
    }

    public function getBalanceProperty ( $invoices )
    {
        $data = collect( $invoices );

        return number_format( $data->sum( 'total_amount' ) - $data->sum( 'adjustment_damaged' ) - $data->sum( 'adjustment_storage' ) - $data->sum( 'adjustment_discount' ) - $data->sum( 'adjustment_other' ) - $data->sum( 'paid_amount' ), 2 );
    }
}

