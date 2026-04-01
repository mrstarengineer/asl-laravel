<?php

namespace App\Exports;

use App\Services\Consignee\ConsigneeService;
use App\Services\Invoice\InvoiceService;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class InvoicesExport implements FromCollection, WithMapping, WithHeadings
{
    private $filters = [];

    /**
     * InvoicesExport constructor.
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
        return app( InvoiceService::class )->all( array_merge( $this->filters, [ 'limit' => -1 ] ) );
    }

    public function headings (): array
    {
        return [
            'INVOICE ID',
            'DATE',
            'COMPANY NAME',
            'CONTAINER NO',
            'AR NO',
            'TOTAL AMOUNT',
            'DAMAGE',
            'STORAGE',
            'DISCOUNT',
            'OTHER',
            'PAID AMOUNT',
            'BALANCE',
            'NOTE',
        ];
    }

    public function map ( $row ): array
    {
        return [
            $row->id,
            Carbon::parse( $row->created_at )->format( 'Y-m-d' ),
            data_get( $row, 'customer.company_name' ),
            data_get( $row, 'export.container_number' ),
            data_get( $row, 'export.ar_number' ),
            number_format( $row->total_amount, 2 ),
            number_format( $row->adjustment_damaged, 2 ),
            number_format( $row->adjustment_storage, 2 ),
            number_format( $row->adjustment_discount, 2 ),
            number_format( $row->adjustment_other, 2 ),
            number_format( $row->paid_amount, 2 ),
            number_format( $row->total_amount - $row->adjustment_damaged - $row->adjustment_storage - $row->adjustment_discount - $row->adjustment_other - $row->paid_amount, 2 ),
            $row->note,
        ];
    }
}
