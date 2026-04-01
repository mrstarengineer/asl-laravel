<?php

namespace App\Presenters;


use App\Transformer\LoadStatusTransformer;

class LoadingStatusPresenter extends BasePresenter
{
    public function present (): array
    {
        return [
            'id',
            'legacy_customer_id',
            'customer_user_id',
            'customer_name',
            'on_hand',
            'exportable',
            'no_title',
            'on_the_way',
            'pending',
            'bos',
            'lien',
            'rejected',
            'mv907',
            'load_status' => 'exportable',
            'loading_instruction',
        ];
    }

    public function transformer ()
    {
        return LoadStatusTransformer::class;
    }
}
