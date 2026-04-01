<?php

namespace App\Exports;

use App\Services\Reports\ReportService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class VehicleReportExport implements FromCollection, WithMapping, WithHeadings
{
    private $filters = [];

    /**
     * VehicleReportExport constructor.
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
        return app( ReportService::class )->vehicleReport( array_merge( $this->filters, [ 'limit' => -1 ] ) );
    }

    public function headings (): array
    {
        return [
            'Year',
            'Make',
            'Model',
            'Color',
            'Vin',
            'Lot Number',
            'Customer Name',
            'Location',
            'Status',
        ];
    }

    public function map ( $row ): array
    {
        return [
            $row->year,
            $row->make,
            $row->model,
            $row->color,
            $row->vin,
            $row->lot_number,
            $row->customer_name,
            $row->location_name,
            trans( 'vehicle.statuses.' . $row->status ),
        ];
    }
}
