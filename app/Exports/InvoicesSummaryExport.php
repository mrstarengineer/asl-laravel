<?php

namespace App\Exports;

use App\Services\Consignee\ConsigneeService;
use App\Services\Invoice\InvoiceService;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class InvoicesSummaryExport implements FromCollection, WithMapping, WithHeadings
{
    private $filters = [];

    /**
     * InvoicesSummaryExport constructor.
     *
     * @param array $filters
     */
    public function __construct ( array $filters = [] )
    {
        $this->filters = $filters;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection ()
    {
        return app( InvoiceService::class )->summaryData( array_merge( $this->filters, [ 'limit' => -1 ] ) );
    }

    public function headings (): array
    {
        return [
            'COMPANY NAME',
            'TOTAL AMOUNT',
            'PAID AMOUNT',
            'DISCOUNT',
            'BALANCE',
        ];
    }

    public function map ( $row ): array
    {
        return [
            $row->company_name,
            number_format( $row->invoices->sum( 'total_amount' ), 2 ),
            number_format( $row->invoices->sum( 'paid_amount' ), 2 ),
            number_format( $row->invoices->sum( 'adjustment_discount' ), 2 ),
            number_format( $row->invoices->sum( 'total_amount' ) - $row->invoices->sum( 'adjustment_damaged' ) - $row->invoices->sum( 'adjustment_storage' ) - $row->invoices->sum( 'adjustment_discount' ) - $row->invoices->sum( 'adjustment_other' ) - $row->invoices->sum( 'paid_amount' ), 2 ),
        ];
    }
}
