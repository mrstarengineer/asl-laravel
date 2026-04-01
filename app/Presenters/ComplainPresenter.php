<?php

namespace App\Presenters;


class ComplainPresenter extends BasePresenter
{
    public function present (): array
    {
        return [
            'id',
            'customer_user_id',
            'subject',
            'customer_name' => 'customer.customer_name',
            'message',
            'status',
            'read_by_admin',
            'created_at',
            'conversations' => [ ConversationPresenter::class => [ 'conversations' ] ],
        ];
    }
}
