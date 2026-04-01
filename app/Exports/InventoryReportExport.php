<?php

namespace App\Exports;

use App\Enums\Roles;
use App\Enums\VehicleStatus;
use App\Services\Reports\ReportService;
use App\Services\Vehicle\VehicleService;
use Illuminate\Database\Query\Builder;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class InventoryReportExport implements FromQuery, WithMapping, WithHeadings
{
    use Exportable;

    private $filters = [];

    /**
     * CustomerManagementReportExport constructor.
     *
     * @param array $filters
     */
    public function __construct ( array $filters = [] )
    {
        $this->filters = $filters;
    }

    public function headings (): array
    {

        $headings = [
            'Hat Number',
            'Company Name',
            'Customer Name',
            'Date Received',
            'Year',
            'Make',
            'Model',
            'Color',
            'Vin',
            'Title',
            'Title Type',
            'Keys',
            'Age',
            'Notes',
            'Status',
            'Location',
            'Container Number',
            'Eta',
            'Ar Number',
            'Booking Number',
            'Export Date',
            'Location',
            'Hybrid',
        ];

        if (app( VehicleService::class )->isShowYard()) {
            $headings[] = 'Yard';
        }

        return $headings;
    }

    public function map ( $row ): array
    {
        $columns = [
            $row->hat_number,
            optional($row->customer)->company_name,
            optional($row->customer)->customer_name,
            $row->status == VehicleStatus::ON_THE_WAY ? optional($row->towing_request)->towing_request_date : optional($row->towing_request)->deliver_date,
            $row->year,
            $row->make,
            $row->model,
            $row->color,
            $row->vin,
            optional($row->towing_request)->title_received ? 'Yes' : 'No',
            trans( 'vehicle.title_type.' . data_get( $row, 'towing_request.title_type', 0 ) ),
            $row->keys ? 'Yes' : 'No',
            $row->age,
            $row->note,
            trans( 'vehicle.statuses.' . $row->status ),
            optional($row->location)->name,
            data_get( $row, 'export.container_number' ),
            data_get( $row, 'export.eta' ),
            data_get( $row, 'export.ar_number' ),
            data_get( $row, 'export.booking_number' ),
            data_get( $row, 'export.export_date' ),
            data_get( $row, 'location.name' ),
            $this->hybridName(data_get( $row, 'hybrid' )),
        ];

        if (app( VehicleService::class )->isShowYard()) {
            $columns[] = data_get( $row, 'yard.name' );
        }

        return $columns;

    }

    public function query ()
    {
        return app( ReportService::class )->inventoryReport( array_merge( $this->filters, [ 'query_only' => true ] ) );
    }

    public function hybridName($hybrid)
    {
        if($hybrid == 1) {
            return 'Yes';
        }else if($hybrid == 2) {
            return 'No';
        }
        return '';
    }
}
