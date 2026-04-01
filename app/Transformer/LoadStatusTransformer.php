<?php

namespace App\Transformer;

use App\Enums\Roles;
use Illuminate\Support\Arr;

class LoadStatusTransformer extends \Nahid\Presento\Transformer
{
    public function getLoadStatusProperty ( $value )
    {
        return $value > 3 ? 'YES' : 'NO';
    }

    public function getLoadingInstructionProperty ()
    {
        return '';
    }

}

