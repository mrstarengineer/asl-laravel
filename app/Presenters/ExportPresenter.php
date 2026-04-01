<?php

namespace App\Presenters;


use App\Transformer\ExportTransformer;
use Nahid\Presento\Presenter;

class ExportPresenter extends Presenter
{
    public function present(): array
    {
        return [
            'id',
            'thumbnail'              => 'export_images.0.thumbnail',
            'photos_count'           => 'export_images_count',
            'vehicles_count',
            'loading_date',
            'export_date',
            'eta',
            'booking_number',
            'container_number',
            'streamship_line',
            'container_tracking_url' => 'container_number',
            'container_type',
            'broker_name',
            'container_type_name'    => 'container_type',
            'port_of_loading',
            'port_of_loading_name'   => 'port_of_loading',
            'port_of_discharge',
            'port_of_discharge_name' => 'port_of_discharge',
            'ar_number',
            'status',
            'status_name'            => 'status',
            'manifest_date'          => 'created_at',
            'customer_name'          => 'customer.customer_name',
            'consignee_name'         => 'houstan_custom_cover_letter.consignee_item.consignee_name',
            'terminal',
            'vessel',
            'destination',
            'export_invoice',
            'export_invoice_photo'   => 'export_invoice',
            'dxb_inv'                => 'invoice.upload_invoice',
            'customer_user_id',
            'invoice_details'        => 'invoice',
            'hybrid_exist'           => 'vehicles',
            'diff_days'              => 'created_at',
            'note',
            'vehicles'               => [ VehiclePresenter::class => [ 'vehicles' ] ],
        ];
    }

    public function transformer()
    {
        return ExportTransformer::class;
    }
}
