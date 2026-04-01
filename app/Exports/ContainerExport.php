<?php

namespace App\Exports;

use App\Services\Export\ExportService;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ContainerExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    private $filters = [];

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
            'Status',
            'Booking Number',
            'Container Number',
            'Ar Number',
            'Created At',
            'Port Of Loading',
            'Port Of Discharge',
            'Yard',
            'Customer Name',
            'Legacy Customer Id',
            'Terminal',
            'Vessel',
            'Container Type',
            'Export Photos',
            'No Of Vehicles',
            'Note',
        ];
    }

    public function map ( $row ): array
    {
        return [
            $row->loading_date,
            $row->export_date,
            $row->eta,
            array_key_exists( $row->status, trans( 'exports.statuses' )) ? trans( 'exports.statuses.' . $row->status ) : '',
            $row->booking_number,
            $row->container_number,
            $row->ar_number,
            $row->created_at,
            array_key_exists( $row->port_of_loading, trans( 'exports.port_of_loadings' ) ) ? trans( 'exports.port_of_loadings.' . $row->port_of_loading ) : '',
            array_key_exists( $row->port_of_discharge, trans( 'exports.port_of_discharges' ) ) ? trans( 'exports.port_of_discharges.' . $row->port_of_discharge ) : '',
            data_get( $row, 'vehicles.0.yard.name' ),
            data_get( $row, 'customer.customer_name' ),
            data_get( $row, 'customer.legacy_customer_id' ),
            $row->terminal,
            $row->vessel,
            array_key_exists( $row->container_type, trans( 'exports.container_types' ) ) ? trans( 'exports.container_types.' . $row->container_type ) : '',
            $row->export_images_count,
            $row->vehicles_count,
            $row->note,
        ];
    }

    public function query ()
    {
        return app( ExportService::class )->all( array_merge( $this->filters, [ 'limit' => -1, 'query_only' => true ] ) );
    }
}
