<?php

namespace App\Exports;

use App\Services\Vehicle\VehicleService;
use App\Services\VehicleWeight\VehicleWeightService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class VehicleWeightExport implements FromCollection, WithHeadings, WithMapping
{
    private $filters = [];

    public function __construct ( array $filters = [] )
    {
        $this->filters = $filters;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection ()
    {
        return app( VehicleWeightService::class )->all( array_merge( $this->filters, [ 'limit' => -1 ] ) );
    }

    public function headings (): array
    {
        return [
            'Year',
            'Maker',
            'Model',
            'Weight',
            'Created At',
            'Updated At',
        ];
    }

    public function map ( $row ): array
    {
        return [
            $row->year,
            $row->maker,
            $row->model,
            $row->weight,
            $row->created_at,
            $row->updated_at,
        ];
    }
}
