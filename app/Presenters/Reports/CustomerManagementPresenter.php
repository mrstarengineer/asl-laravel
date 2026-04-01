<?php

namespace App\Presenters\Reports;


use App\Presenters\BasePresenter;
use App\Transformer\Reports\CustomerManagementTransformer;

class CustomerManagementPresenter extends BasePresenter
{
    public function present(): array
    {
        return [
            'id',
            'user_id',
            'customer_name',
            'company_name',
            'legacy_customer_id',
            'on_hand',
            'manifest',
            'on_the_way',
            'shipped_cars',
            'manifest_cars',
            'total_cars',
            'total_current_cars',
            'total_value_of_vehicles',
            'pending_amount' => 'invoices',
        ];
    }

    public function transformer()
    {
        return CustomerManagementTransformer::class;
    }
}
