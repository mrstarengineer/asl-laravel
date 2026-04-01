<?php

namespace App\Presenters;


use App\Transformer\NoteTransformer;

class NotePresenter extends BasePresenter
{
    public function present (): array
    {
        return [
            'id',
            'description',
            'vehicle_id',
            'export_id',
            'image_url',
            'created_by' => 'user',
            'vehicle' => [VehiclePresenter::class => ['vehicle']],
            'admin_view',
            'cust_view',
            'created_at',
            'updated_at',
        ];
    }

    public function transformer ()
    {
        return NoteTransformer::class;
    }
}
