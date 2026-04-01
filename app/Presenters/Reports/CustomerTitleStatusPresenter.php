<?php

namespace App\Presenters\Reports;


use App\Presenters\BasePresenter;
use App\Transformer\Reports\CustomerManagementTransformer;
use App\Transformer\Reports\CustomerTitleStatusTransformer;

class CustomerTitleStatusPresenter extends BasePresenter
{
    public function present (): array
    {
        return [
            'user_id',
            'customer_name',
            'legacy_customer_id',
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
            'loading_instructions',
        ];
    }

    public function transformer ()
    {
        return CustomerTitleStatusTransformer::class;
    }
}
