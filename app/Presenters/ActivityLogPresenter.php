<?php

namespace App\Presenters;


use App\Transformer\ActivityLogTransformer;

class ActivityLogPresenter extends BasePresenter
{
    public function present (): array
    {
        return [
            'id',
            'user_id',
            'model_id',
            'title',
            'platform',
            'type',
            'created_at',
        ];
    }

    public function transformer ()
    {
        return ActivityLogTransformer::class;
    }
}
