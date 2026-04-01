<?php

namespace App\Exports;

use App\Enums\Roles;
use App\Enums\VehicleStatus;
use App\Services\Vehicle\VehicleService;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class VehiclesExport implements FromQuery, WithHeadings, WithMapping
{
    private $filters = [];

    public function __construct( array $filters = [] )
    {
        $this->filters = $filters;
    }

    public function headings(): array
    {
        $headings = [
            'Year',
            'Make',
            'Model',
            'Color',
            'Vin',
            'Lot Number',
            'Customer Name',
            'Location',
            'Hybrid',
            'Value',
            'Status',
            'Days',
            'Hat Number',
            'Yard Photos',
            'Pickup Photos',
            'Tow Req Date',
            'Vehicle Delivery Date',
            'Title Type',
            'Title Received Date',
            'Vehicle Type',
            'Buyer Id',
            'Keys',
            'Manifest Date',
            'Container Number',
            'Created At',
            'Loading Status',
            'Admin Note',
        ];

        if (app( VehicleService::class )->isShowYard()) {
            $headings[] = 'Yard';
        }


        return $headings;
    }

    public function map( $row ): array
    {
        $columns = [
            $row->year,
            $row->make,
            $row->model,
            $row->color,
            $row->vin,
            $row->lot_number,
            data_get( $row, 'customer.customer_name' ),
            data_get( $row, 'location.name' ),
            $this->hybridName(data_get( $row, 'hybrid' )),
            $row->value,
            trans( 'vehicle.statuses.' . $row->status ),
            $row->age,
            $row->hat_number,
            $row->vehicle_image_count,
            $row->pickup_photos_count,
            data_get( $row, 'towing_request.towing_request_date', '' ),
            data_get( $row, 'towing_request.deliver_date' ),
            trans( 'vehicle.title_type.' . data_get( $row, 'towing_request.title_type', 0 ) ),
            data_get( $row, 'towing_request.title_received_date' ),
            $row->vehicle_type,
            $row->license_number,
            $row->keys ? 'Yes' : 'No',
            data_get( $row, 'export.created_at' ) ? date( 'Y-m-d', strtotime( $row->export->created_at ) ) : '',
            $row->container_number,
            $row->created_at,
            ( $row->status == VehicleStatus::ON_HAND && ( data_get( $row, 'towing_request.title_type' ) == 1 || data_get( $row, 'towing_request.title_received_date' ) == 1 ) ) ? strtoupper( $row->load_status ) : '',
            $row->note,
        ];


        if (app( VehicleService::class )->isShowYard()) {
            $columns[] = data_get( $row, 'yard.name' );
        }

        return $columns;
    }

    public function query()
    {
        return app( VehicleService::class )->all( array_merge( $this->filters, [ 'limit' => -1, 'query_only' => true ] ) );
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
