<?php

namespace App\Transformer\Reports;


class CustomerManagementTransformer extends \Nahid\Presento\Transformer
{
    public function getPendingAmountProperty ( $invoices ): float
    {
        $invoices = collect( $invoices );

        return round( $invoices->sum( 'total_amount' ) - $invoices->sum( 'adjustment_damaged' )
            - $invoices->sum( 'adjustment_storage' ) - $invoices->sum( 'adjustment_discount' )
            - $invoices->sum( 'adjustment_other' ) - $invoices->sum( 'paid_amount' ), 2 );
    }
}

