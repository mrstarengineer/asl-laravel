<?php

namespace App\Presenters;


class CityPresenter extends BasePresenter
{
    public function present(): array
    {
        return [
            'id',
            'state_id',
            'name',
            'short_code',
            'state_name'=>'state.name',
            'status',
        ];
    }
}
