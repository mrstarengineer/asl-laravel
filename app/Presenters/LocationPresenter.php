<?php

namespace App\Presenters;


class LocationPresenter extends BasePresenter
{
    public function present(): array
    {
        return [
            'id',
            'name',
            'status',
        ];
    }
}
