<?php

namespace App\Presenters;


class CountryPresenter extends BasePresenter
{
    public function present(): array
    {
        return [
            'id',
            'name',
            'short_code',
            'status',
        ];
    }
}
