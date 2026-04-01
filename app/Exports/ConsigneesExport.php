<?php

namespace App\Exports;

use App\Services\Consignee\ConsigneeService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ConsigneesExport implements FromQuery, WithMapping, WithHeadings
{
    private $filters = [];

    /**
     * ConsigneesExport constructor.
     *
     * @param array $filters
     */
    public function __construct ( array $filters = [] )
    {
        $this->filters = $filters;
    }

    public function query ()
    {
        return app( ConsigneeService::class )->all( array_merge( $this->filters, [ 'limit' => -1, 'query_only' => true ] ) );
    }

    public function headings (): array
    {
        return [
            'Consignee Name',
            'Customer Name',
            'Address1',
            'Address2',
            'Country',
            'State',
            'City',
            'Phone',
            'Zip Code',
        ];
    }

    public function map ( $row ): array
    {
        return [
            $row->consignee_name,
            data_get($row->customer, 'customer_name'),
            $row->consignee_address_1,
            $row->consignee_address_2,
            data_get($row->country, 'name'),
            data_get($row->state, 'name'),
            data_get($row->city, 'name'),
            $row->phone,
            $row->zip_code,
        ];
    }
}
