<?php

namespace App\Presenters;


use App\Transformer\CustomerTransformer;

class CustomerPresenter extends BasePresenter {
	public function present(): array {
		return [
			'id',
			'user_id',
			'customer_name',
			'company_name',
			'username' => 'user.username',
            'email' => 'user.email',
            'status' => 'user.status',
            'status_name' => 'user.status',
            'inactive_at' => 'user.inactive_at',
            'role_name' => 'user.role',
			'phone',
			'photo' => 'user.photo',
			'phone_two',
			'address_line_1',
			'address_line_2',
			'city_id',
			'state_id',
			'country_id',
			'zip_code',
			'tax_id',
			'fax',
			'trn',
			'other_emails',
			'note',
			'legacy_customer_id',
			'loading_type',
			'loading_type_title' => 'loading_type',
			'customer_documents',
            'consignees',
            'vehicles_count',
            'exports_count',
			'created_by',
			'created_at',
			'updated_by',
		];
	}

	public function transformer()
	{
		return CustomerTransformer::class;
	}
}
