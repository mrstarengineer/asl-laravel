<?php

namespace App\Presenters;


use App\Transformer\UserTransformer;

class UserPresenter extends BasePresenter
{
    public function present(): array
    {
        return [
            'id',
            'username',
            'email',
            'photo',
            'status',
            'locations',
            'customers',
            'location_names' => 'locations',
            'company_name'   => 'customer.company_name',
            'role',
            'role_name'      => 'role',
            'email_verified_at',
            'created_at',
            'updated_at',
        ];
    }

    public function transformer()
    {
        return UserTransformer::class;
    }
}
