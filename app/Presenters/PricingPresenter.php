<?php

namespace App\Presenters;


use App\Transformer\PricingTransformer;

class PricingPresenter extends BasePresenter {
	public function present(): array {
		return [
            'id',
            'upload_file',
            'file_url'          => 'upload_file',
            'file_name',
            'file_size',
            'file_type',
            'month',
            'month_name'        => 'month',
            'str_month',
            'pricing_type',
            'pricing_type_name' => 'pricing_type',
            'status',
            'status_name'       => 'status',
            'description',
            'created_by',
            'created_at',
            'updated_by',
            'updated_at',
		];
	}

	public function transformer()
	{
		return PricingTransformer::class;
	}
}
