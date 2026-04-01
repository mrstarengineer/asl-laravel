<?php

namespace App\Presenters;


class NotificationPresenter extends BasePresenter {
	public function present(): array {
		return [
			'id',
			'subject',
			'message',
			'is_read',
			'status',
			'created_by',
			'updated_by',
			'user_id',
			'expire_date',
			'only_notify',
            'created_at',
		];
	}
}
