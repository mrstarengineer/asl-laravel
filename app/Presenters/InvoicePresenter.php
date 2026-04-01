<?php

namespace App\Presenters;


use App\Transformer\InvoiceTransformer;

class InvoicePresenter extends BasePresenter
{
    public function present (): array
    {
        return [
            'id',
            'version_id',
            'customer_user_id',
            'export_id',
            'date'                   => 'created_at',
            'company_name'           => 'customer.company_name',
            'container_number'       => 'export.container_number',
            'ar_number'              => 'export.ar_number',
            'total_amount',
            'adjustment_damaged',
            'adjustment_storage',
            'adjustment_discount',
            'adjustment_other',
            'paid_amount',
            'discount',
            'balance',
            'note',
            'upload_invoice',
            'upload_invoice_item'    => 'upload_invoice',
            'clearance_invoice',
            'clearance_invoice_item' => 'clearance_invoice',
            'usa_invoice'            => 'export.export_invoice',
            'documents',
            'status',
            'status_name',
            'upload_invoice_type',
            'upload_invoice_size',
            'upload_invoice_name',
            'clearance_invoice_type',
            'clearance_invoice_size',
            'clearance_invoice_name',
        ];
    }

    public function transformer ()
    {
        return InvoiceTransformer::class;
    }
}
