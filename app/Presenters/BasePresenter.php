<?php

namespace App\Presenters;

use Illuminate\Database\Eloquent\Model;
use Nahid\Presento\Presenter;

abstract class BasePresenter extends Presenter
{
    public function convert($data)
    {
        if ($data instanceof Model) {
            return $data->toArray();
        }

        if (isset($data['data'])) {
            return $data['data'];
        }

        return $data;
    }
}
