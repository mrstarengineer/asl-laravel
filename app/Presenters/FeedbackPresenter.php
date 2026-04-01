<?php

namespace App\Presenters;


class FeedbackPresenter extends BasePresenter
{
    public function present(): array
    {
        return [
            'id',
            'customer_user_id',
            'customer_name' => 'customer.customer_name',
            'message',
            'note',
            'status',
            'read_by_admin',
            'created_at',
        ];
    }
}
