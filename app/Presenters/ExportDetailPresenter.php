<?php

namespace App\Presenters;


use App\Transformer\ExportTransformer;
use Nahid\Presento\Presenter;

class ExportDetailPresenter extends Presenter
{
    public function present(): array
    {
        return [
            'id',
            'version_id',
            'customer_user_id',
            'shipper_id',
            'shipper_name'           => 'exporter.customer_name',
            'export_date',
            'loading_date',
            'broker_name',
            'booking_number',
            'eta',
            'status',
            'ar_number',
            'xtn_number',
            'seal_number',
            'handed_over_date',
            'container_number',
            'cutt_off',
            'vessel',
            'voyage',
            'destination',
            'terminal',
            'streamship_line',
            'container_type',
            'container_type_name'    => 'container_type',
            'port_of_loading',
            'port_of_loading_name'   => 'port_of_loading',
            'port_of_discharge',
            'port_of_discharge_name' => 'port_of_discharge',
            'bol_note',
            'bol_remarks',
            'special_instruction',
            'contact_details',
            'itn',
            'oti_number',
            'note',
            'customer',
            'houstan_custom_cover_letter',
            'dock_receipt',
            'export_invoice',
            'export_invoice_photo'   => 'export_invoice',
            'container_images'       => 'export_images',
            'empty_container_photos',
            'loading_photos',
            'loaded_photos',
            'vehicle_ids'            => 'vehicles',
            'manifest_date'          => 'created_at',
        ];
    }

    public function transformer()
    {
        return ExportTransformer::class;
    }
}
