<?php

namespace App\Exports;

use App\Enums\VisibilityStatus;
use App\Services\Customer\CustomerService;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CustomersExport implements FromQuery, WithMapping, WithHeadings
{
    use Exportable;

    private $filters = [];

    /**
     * CustomersExport constructor.
     *
     * @param array $filters
     */
    public function __construct( array $filters = [] )
    {
        $this->filters = $filters;
    }

    public function query()
    {
        return app( CustomerService::class )->all( array_merge( $this->filters, [ 'limit' => -1, 'query_only' => true ] ) );
    }

    /**
     * @param $row
     *
     * @return array
     */
    public function map( $row ): array
    {
        return [
            $row->legacy_customer_id,
            $row->customer_name,
            $row->company_name,
            optional( $row->user )->email,
            $row->phone,
            $row->loading_type == 1 ? 'Full' : ( $row->loading_type == 2 ? 'Mix' : '' ),
            data_get( $row, 'user.status' ) == VisibilityStatus::ACTIVE ? 'Active' : 'Inactive',
            $row->vehicles_count,
            $row->exports_count,
            $row->created_at,
        ];
    }

    public function headings(): array
    {
        return [
            'Customer ID',
            'Name',
            'Company Name',
            'Email',
            'Phone UAE',
            'Loading Type',
            'Status',
            'Total Vehicles',
            'Total Containers',
            'Created At',
        ];
    }
}
