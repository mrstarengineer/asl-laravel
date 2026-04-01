<?php

namespace App\Presenters;


class YardPresenter extends BasePresenter
{
    public function present(): array
    {
        return [
            'id',
            'location_id',
            'name',
            'location_name' => 'location.name',
            'status',
        ];
    }
}
