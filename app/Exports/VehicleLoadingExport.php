<?php

namespace App\Exports;

use App\Services\Customer\CustomerService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class VehicleLoadingExport implements FromCollection, WithMapping, WithHeadings
{
    private $filters = [];

    /**
     * VehicleLoadingExport constructor.
     * @param array $filters
     */
    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return app(CustomerService::class)->vehicleLoading(array_merge($this->filters, ['limit' => -1]));
    }

    /**
     * @param $row
     * @return array
     */
    public function map($row): array
    {
        return [
            $row->customer_name,
            $row->legacy_customer_id,
            $row->on_the_way,
            $row->on_hand,
            $row->no_title,
            $row->exportable,
            $row->pending,
            $row->bos,
            $row->rejected,
            $row->mv907,
            $row->exportable > 3 ? 'YES' : 'NO',
        ];
    }

    public function headings(): array
    {
        return [
            'CUSTOMER',
            'Customer User ID',
            'ON THE WAY',
            'ON HAND',
            'NO TITLE',
            'EXPORTABLE',
            'PENDING',
            'BOS',
            'LIEN',
            'REJECTED',
            'MV907',
            'LOAD STATUS',
        ];
    }
}
