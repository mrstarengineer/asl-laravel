<?php

namespace App\Presenters;


class StatePresenter extends BasePresenter
{
    public function present(): array
    {
        return [
            'id',
            'country_id',
            'name',
            'short_code',
            'country_name' => 'country.name',
            'status',
        ];
    }
}
