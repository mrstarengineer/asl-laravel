<?php

namespace App\Transformer;

use Carbon\Carbon;

class ActivityLogTransformer extends \Nahid\Presento\Transformer
{
    public function getCreatedAtProperty($value)
    {
        return Carbon::parse($value, 'UTC')
            ->timezone('Asia/Dubai')
            ->format('M d, Y h:i A');
    }

}

