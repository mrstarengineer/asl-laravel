<?php

namespace App\Presenters;


use App\Transformer\InvoiceSummaryTransformer;
use App\Transformer\InvoiceTransformer;

class InvoiceSummaryPresenter extends BasePresenter
{
    public function present (): array
    {
        return [
            'user_id',
            'legacy_customer_id',
            'customer_name',
            'company_name',
            'total_amount' => 'invoices',
            'paid_amount'  => 'invoices',
            'discount'     => 'invoices',
            'balance'      => 'invoices',
        ];
    }

    public function transformer ()
    {
        return InvoiceSummaryTransformer::class;
    }
}
