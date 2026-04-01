<?php

namespace App\Presenters;


use App\Transformer\VehicleTransformer;
use Nahid\Presento\Presenter;

class VehicleWeightPresenter extends Presenter
{
    public function present (): array
    {
        return [
            'id',
            'year',
            'maker',
            'model',
            'weight',
        ];
    }
}
