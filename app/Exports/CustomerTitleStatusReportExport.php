<?php

namespace App\Exports;

use App\Services\Reports\ReportService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CustomerTitleStatusReportExport implements FromCollection, WithMapping, WithHeadings
{
    private $filters = [];

    /**
     * CustomerTitleStatusReportExport constructor.
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
        return app( ReportService::class )->customerTitleStatusReport( array_merge( $this->filters, [ 'limit' => -1 ] ) );
    }

    public function headings (): array
    {
        return [
            'CUSTOMER USER ID',
            'CUSTOMER NAME',
            'ON HAND',
            'ON THE WAY',
            'NO TITLE',
            'EXPORTABLE',
            'PENDING',
            'BOS',
            'LIEN',
            'REJECTED',
            'MV907',
            'LOAD STATUS',
            'LOADING INSTRUCTIONS',
        ];
    }

    public function map ( $row ): array
    {
        return [
            $row->user_id,
            $row->customer_name,
            $row->on_hand,
            $row->on_the_way,
            $row->no_title,
            $row->exportable,
            $row->pending,
            $row->bos,
            $row->lien,
            $row->rejected,
            $row->mv907,
            $row->exportable > 3 ? 'YES' : 'NO',
            ''
        ];
    }
}
