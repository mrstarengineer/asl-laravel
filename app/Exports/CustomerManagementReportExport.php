<?php

namespace App\Exports;

use App\Services\Reports\ReportService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CustomerManagementReportExport implements FromCollection, WithMapping, WithHeadings
{
    private $filters = [];

    /**
     * CustomerManagementReportExport constructor.
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
        return app( ReportService::class )->customerManagementReport( array_merge( $this->filters, [ 'limit' => -1 ] ) );
    }

    public function headings (): array
    {
        return [
            'CUSTOMER USER ID',
            'COMPANY NAME',
            'CUSTOMER NAME',
            'AMOUNT PENDING FOR PAYMENT',
            'ON THE WAY',
            'ON HAND',
            'MANIFEST',
            'SHIPPED',
            'TOTAL CURRENT CARS',
            'TOTAL CARS',
            'Value of Total Cars',
        ];
    }

    public function map ( $row ): array
    {
        return [
            $row->user_id,
            $row->customer_name,
            $row->company_name,
            round( $row->invoices->sum( 'total_amount' ) - $row->invoices->sum( 'adjustment_damaged' )
                - $row->invoices->sum( 'adjustment_storage' ) - $row->invoices->sum( 'adjustment_discount' )
                - $row->invoices->sum( 'adjustment_other' ) - $row->invoices->sum( 'paid_amount' ), 2 ),
            $row->on_the_way,
            $row->on_hand,
            $row->manifest,
            $row->shipped,
            $row->on_the_way + $row->on_hand + $row->manifest + $row->shipped,
            $row->total_cars,
            $row->total_value_of_vehicles,
        ];
    }
}
