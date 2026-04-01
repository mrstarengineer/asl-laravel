<?php

namespace App\Transformer\Reports;


use Illuminate\Support\Arr;

class CustomerTitleStatusTransformer extends \Nahid\Presento\Transformer
{
    public function getLoadStatusProperty ( $value ): string
    {
        return $value > 3 ? 'Yes' : 'No';
    }

    public function getLoadingInstructionsProperty (): string
    {
        return '';
    }
}

