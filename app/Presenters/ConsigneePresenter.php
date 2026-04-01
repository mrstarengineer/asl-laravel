<?php

namespace App\Presenters;


class ConsigneePresenter extends BasePresenter {
	public function present(): array {
		return [
			'id',
            'version_id',
			'customer_user_id',
			'consignee_name',
            'customer_name' => 'customer.customer_name',
            'company_name' => 'customer.company_name',
			'consignee_address_1',
			'consignee_address_2',
            'country_id',
            'state_id',
            'city_id',
            'zip_code',
			'phone',
			'country_name' => 'country.name',
			'state_name' => 'state.name',
			'city_name' => 'city.name',
		];
	}
}
