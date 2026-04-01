<?php

namespace App\Presenters;


class ActivityLogDetailPresenter extends BasePresenter
{
    public function present (): array
    {
        return [
            'id',
            'user_id',
            'model_id',
            'title',
            'platform',
            'logs',
            'request_data',
            'type',
            'created_at',
        ];
    }
}
