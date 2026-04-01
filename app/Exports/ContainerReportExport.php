<?php

namespace App\Exports;

use App\Services\Reports\ReportService;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ContainerReportExport implements FromQuery, WithMapping, WithHeadings
{
    private $filters = [];

    /**
     * ContainerReportExport constructor.
     * @param array $filters
     */
    public function __construct ( array $filters = [] )
    {
        $this->filters = $filters;
    }

    public function headings (): array
    {
        return [
            'Loading Date',
            'Export Date',
            'Eta',
            'Booking Number',
            'Container Number',
            'Customer Name',
            'Terminal',
            'Vessel',
        ];
    }

    public function map ( $row ): array
    {
        return [
            $row->loading_date,
            $row->export_date,
            $row->eta,
            $row->booking_number,
            $row->container_number,
            $row->customer_name,
            $row->terminal,
            $row->vessel,
        ];
    }

    public function query ()
    {
        return app( ReportService::class )->containerReport( array_merge( $this->filters, [ 'limit' => -1, 'query_only' => true ] ) );
    }
}
