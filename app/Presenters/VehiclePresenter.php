<?php

namespace App\Presenters;


use App\Transformer\VehicleTransformer;
use Nahid\Presento\Presenter;

class VehiclePresenter extends Presenter
{
    public function present (): array
    {
        return [
            'id',
            'photo'                  => 'vehicle_image.0.thumbnail',
            'photo_urls'             => 'vehicle_image',
            'total_photos'           => 'vehicle_image_count',
            'hat_number',
            'vcr',
            'towing_request_date'    => 'towing_request.towing_request_date',
            'deliver_date'           => 'towing_request.deliver_date',
            'age',
            'year',
            'make',
            'model',
            'color',
            'vin',
            'lot_number',
            'buyer_id'               => 'license_number',
            'keys_name'              => 'keys',
            'vehicle_type',
            'title_type_name'        => 'towing_request.title_type',
            'title_received_date'    => 'towing_request.title_received_date',
            'title_number'           => 'towing_request.title_number',
            'location'               => 'location.name',
            'yard_name'              => 'yard.name',
            'location_id'            => 'location.id',
            'status',
            'status_name'            => 'status',
            'container_number'       => 'export.container_number',
            'export_id',
            'handed_over_date',
            'eta'                    => 'export.eta',
            'customer_user_id',
            'customer_name'          => 'customer.customer_name',
            'company_name'           => 'customer.company_name',
            'loading_type'           => 'load_status',
            'created_at',
            'notes_status',
            'note_status'            => 'notes_count',
            'vehicle_documents',
            'invoice_photos',
            'row_color'              => 'status',
            'container_tracking_url' => 'container_number',
            'hybrid'                 => 'hybrid',
        ];
    }

    public function transformer ()
    {
        return VehicleTransformer::class;
    }
}
