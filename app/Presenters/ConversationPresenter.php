<?php

namespace App\Presenters;


use App\Transformer\ConversationTransformer;

class ConversationPresenter extends BasePresenter
{
    public function present (): array
    {
        return [
            'id',
            'model_id',
            'message',
            'sender_id',
            'username' => 'sender.username',
            'class'    => 'sender.role',
            'created_at',
            'updated_at',
        ];
    }

    public function transformer ()
    {
        return ConversationTransformer::class;
    }
}
