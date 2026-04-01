<?php

namespace App\Presenters;


use App\Transformer\ClaimTransformer;

class VehicleClaim extends BasePresenter
{
    public function present (): array
    {
        return [
            'id',
            'vin'           => 'vehicle.vin',
            'customer_name' => 'customer.customer_name',
            'location'      => 'vehicle.location.name',
            'claim_amount',
            'claim_status',
            'status_name'   => 'claim_status',
            'approved_amount',
            'approved_by',
            'approved_date',
            'vehicle_part',
            'damaged_part',
            'other_parts',
            'remarks',
            'admin_remarks',
            'customer_photos',
            'admin_photos',
            'vehicle'       => [ VehiclePresenter::class => [ 'vehicle' ] ],
            'created_at',
            'updated_at',
        ];
    }

    public function transformer ()
    {
        return ClaimTransformer::class;
    }
}
