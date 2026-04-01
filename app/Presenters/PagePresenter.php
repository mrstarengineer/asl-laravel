<?php

namespace App\Presenters;


class PagePresenter extends BasePresenter
{
    public function present(): array
    {
        return [
            'id',
            'title',
            'slug',
            'content',
            'status',
        ];
    }
}
