<?php

namespace App\Transformer;

use App\Enums\StreamshipLine;
use App\Services\Export\ExportService;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class ExportTransformer extends \Nahid\Presento\Transformer
{
    public function getContainerTypeNameProperty( $value )
    {
        return array_key_exists($value, trans('exports.container_types')) ? trans('exports.container_types.' . $value) : '';
    }

    public function getPortOfLoadingNameProperty( $value )
    {
        return array_key_exists($value, trans('exports.port_of_loadings')) ? trans('exports.port_of_loadings.' . $value) : '';
    }

    public function getPortOfDischargeNameProperty( $value )
    {
        return array_key_exists($value, trans('exports.port_of_discharges')) ? trans('exports.port_of_discharges.' . $value) : '';
    }

    public function getStatusNameProperty( $value )
    {
        return array_key_exists($value, trans('exports.statuses')) ? trans('exports.statuses.' . $value) : 'MANIFEST';
    }

    public function getManifestDateProperty( $value ): string
    {
        return Carbon::parse($value)->format('Y-m-d');
    }

    public function getContainerTrackingUrlProperty( $value ): string
    {
        return app(ExportService::class)->trackingUrl($value);
    }

    public function getCustomerProperty( $customer ): array
    {
        return [
            'customer_name'      => data_get($customer, 'customer_name'),
            'user_id'            => data_get($customer, 'user_id'),
            'legacy_customer_id' => data_get($customer, 'legacy_customer_id'),
            'company_name'       => data_get($customer, 'company_name'),
            'email'              => data_get($customer, 'user.email'),
        ];
    }

    public function getHoustanCustomCoverLetterProperty( $data )
    {
        return [
            'vehicle_location'          => Arr::get($data, 'vehicle_location'),
            'exporter_id'               => Arr::get($data, 'exporter_id'),
            'exporter_type_issuer'      => Arr::get($data, 'exporter_type_issuer'),
            'transportation_value'      => Arr::get($data, 'transportation_value'),
            'exporter_dob'              => Arr::get($data, 'exporter_dob'),
            'ultimate_consignee_dob'    => Arr::get($data, 'ultimate_consignee_dob'),
            'consignee'                 => Arr::get($data, 'consignee'),
            'notify_party'              => Arr::get($data, 'notify_party'),
            'menifest_consignee'        => Arr::get($data, 'menifest_consignee'),
            'consignee_detail'          => [ 'id' => Arr::get($data, 'consignee'), 'name' => Arr::get($data, 'consignee_item.consignee_name') ],
            'notify_party_detail'       => [ 'id' => Arr::get($data, 'notify_party'), 'name' => Arr::get($data, 'notify_party_item.consignee_name') ],
            'menifest_consignee_detail' => [ 'id' => Arr::get($data, 'menifest_consignee'), 'name' => Arr::get($data, 'menifest_consignee_item.consignee_name') ],
        ];
    }

    public function getDockReceiptProperty( $data ): array
    {
        return [
            'export_id'                       => Arr::get($data, 'export_id', ''),
            'awb_number'                      => Arr::get($data, 'awb_number', ''),
            'export_reference'                => Arr::get($data, 'export_reference', ''),
            'forwarding_agent'                => Arr::get($data, 'forwarding_agent', ''),
            'domestic_routing_instructions'   => Arr::get($data, 'domestic_routing_instructions', ''),
            'pre_carriage_by'                 => Arr::get($data, 'pre_carriage_by', ''),
            'place_of_receipt_by_pre_carrier' => Arr::get($data, 'place_of_receipt_by_pre_carrier', ''),
            'exporting_carrier'               => Arr::get($data, 'exporting_carrier', ''),
            'final_destination'               => Arr::get($data, 'final_destination', ''),
            'loading_terminal'                => Arr::get($data, 'loading_terminal', ''),
            'dock_container_type'             => Arr::get($data, 'container_type', ''),
            'number_of_packages'              => Arr::get($data, 'number_of_packages', ''),
            'by'                              => Arr::get($data, 'by', ''),
            'date'                            => Arr::get($data, 'date', ''),
            'auto_receiving_date'             => Arr::get($data, 'auto_receiving_date', ''),
            'auto_cut_off'                    => Arr::get($data, 'auto_cut_off', ''),
            'vessel_cut_off'                  => Arr::get($data, 'vessel_cut_off', ''),
            'sale_date'                       => Arr::get($data, 'sale_date', ''),
        ];
    }

    public function getThumbnailProperty( $url )
    {
        if ( empty($url) || !Storage::exists($url) ) {
            return url('images/no-image.png');
        }

//        if ( empty($url) ) {
//            return url('images/no-image.png');
//        }

        return filter_var($url, FILTER_VALIDATE_URL) === false ? Storage::url($url) : $url;
    }

    public function getExportInvoicePhotoProperty( $url ): ?array
    {
        if ( empty($url) || !Storage::exists($url) ) {
            return null;
        }

//        if ( empty($url) ) {
//            return null;
//        }

        return [
            [
                'name' => ( basename(filter_var($url, FILTER_VALIDATE_URL) === false) && $url ) ? Storage::url($url) : $url,
                'url'  => ( filter_var($url, FILTER_VALIDATE_URL) === false && $url ) ? Storage::url($url) : $url,
                'type' => pathinfo($url, PATHINFO_EXTENSION),
                'size' => null,
            ],
        ];
    }

    public function getContainerImagesProperty( $photos )
    {
        return collect($photos)->reject(function ( $item ) {
            return !Storage::exists($item['name']);
        })->map(function ( $item ) {
            return [
                'id'        => $item['id'],
                'name'      => basename(filter_var($item['name'], FILTER_VALIDATE_URL) === false ? Storage::url($item['name']) : $item['name']),
                'thumbnail' => filter_var($item['thumbnail'], FILTER_VALIDATE_URL) === false ? Storage::url( Storage::exists($item['thumbnail']) ? $item['thumbnail'] : $item['name']) : (Storage::exists($item['thumbnail']) ? $item['thumbnail'] : $item['name']),
                'url'       => filter_var($item['name'], FILTER_VALIDATE_URL) === false ? Storage::url($item['name']) : $item['name'],
                'type'      => $item['type'],
                'size'      => $item['size'],
            ];
        })->values()->all();

//        return collect($photos)->map(function ( $item ) {
//            return [
//                'id'        => $item['id'],
//                'name'      => basename(filter_var($item['name'], FILTER_VALIDATE_URL) === false ? Storage::url($item['name']) : $item['name']),
//                'thumbnail' => filter_var($item['thumbnail'], FILTER_VALIDATE_URL) === false ? Storage::url( Storage::exists($item['thumbnail']) ? $item['thumbnail'] : $item['name']) : (Storage::exists($item['thumbnail']) ? $item['thumbnail'] : $item['name']),
//                'url'       => filter_var($item['name'], FILTER_VALIDATE_URL) === false ? Storage::url($item['name']) : $item['name'],
//                'type'      => $item['type'],
//                'size'      => $item['size'],
//            ];
//        })->values()->all();
    }

    public function getEmptyContainerPhotosProperty( $photos )
    {
        return collect($photos)->reject(function ( $item ) {
            return !Storage::exists($item['name']);
        })->map(function ( $item ) {
            return [
                'id'        => $item['id'],
                'name'      => basename(filter_var($item['name'], FILTER_VALIDATE_URL) === false ? Storage::url($item['name']) : $item['name']),
                'thumbnail' => filter_var($item['thumbnail'], FILTER_VALIDATE_URL) === false ? Storage::url( Storage::exists($item['thumbnail']) ? $item['thumbnail'] : $item['name']) : (Storage::exists($item['thumbnail']) ? $item['thumbnail'] : $item['name']),
                'url'       => filter_var($item['name'], FILTER_VALIDATE_URL) === false ? Storage::url($item['name']) : $item['name'],
                'type'      => $item['type'],
                'size'      => $item['size'],
            ];
        })->values()->all();

//        return collect($photos)->map(function ( $item ) {
//            return [
//                'id'        => $item['id'],
//                'name'      => basename(filter_var($item['name'], FILTER_VALIDATE_URL) === false ? Storage::url($item['name']) : $item['name']),
//                'thumbnail' => filter_var($item['thumbnail'], FILTER_VALIDATE_URL) === false ? Storage::url( Storage::exists($item['thumbnail']) ? $item['thumbnail'] : $item['name']) : (Storage::exists($item['thumbnail']) ? $item['thumbnail'] : $item['name']),
//                'url'       => filter_var($item['name'], FILTER_VALIDATE_URL) === false ? Storage::url($item['name']) : $item['name'],
//                'type'      => $item['type'],
//                'size'      => $item['size'],
//            ];
//        })->values()->all();
    }

    public function getLoadingPhotosProperty( $photos )
    {
        return collect($photos)->reject(function ( $item ) {
            return !Storage::exists($item['name']);
        })->map(function ( $item ) {
            return [
                'id'        => $item['id'],
                'name'      => basename(filter_var($item['name'], FILTER_VALIDATE_URL) === false ? Storage::url($item['name']) : $item['name']),
                'thumbnail' => filter_var($item['thumbnail'], FILTER_VALIDATE_URL) === false ? Storage::url( Storage::exists($item['thumbnail']) ? $item['thumbnail'] : $item['name']) : (Storage::exists($item['thumbnail']) ? $item['thumbnail'] : $item['name']),
                'url'       => filter_var($item['name'], FILTER_VALIDATE_URL) === false ? Storage::url($item['name']) : $item['name'],
                'type'      => $item['type'],
                'size'      => $item['size'],
            ];
        })->values()->all();

//        return collect($photos)->map(function ( $item ) {
//            return [
//                'id'        => $item['id'],
//                'name'      => basename(filter_var($item['name'], FILTER_VALIDATE_URL) === false ? Storage::url($item['name']) : $item['name']),
//                'thumbnail' => filter_var($item['thumbnail'], FILTER_VALIDATE_URL) === false ? Storage::url( Storage::exists($item['thumbnail']) ? $item['thumbnail'] : $item['name']) : (Storage::exists($item['thumbnail']) ? $item['thumbnail'] : $item['name']),
//                'url'       => filter_var($item['name'], FILTER_VALIDATE_URL) === false ? Storage::url($item['name']) : $item['name'],
//                'type'      => $item['type'],
//                'size'      => $item['size'],
//            ];
//        })->values()->all();
    }

    public function getLoadedPhotosProperty( $photos )
    {
        return collect($photos)->reject(function ( $item ) {
            return !Storage::exists($item['name']);
        })->map(function ( $item ) {
            return [
                'id'        => $item['id'],
                'name'      => basename(filter_var($item['name'], FILTER_VALIDATE_URL) === false ? Storage::url($item['name']) : $item['name']),
                'thumbnail' => filter_var($item['thumbnail'], FILTER_VALIDATE_URL) === false ? Storage::url( Storage::exists($item['thumbnail']) ? $item['thumbnail'] : $item['name']) : (Storage::exists($item['thumbnail']) ? $item['thumbnail'] : $item['name']),
                'url'       => filter_var($item['name'], FILTER_VALIDATE_URL) === false ? Storage::url($item['name']) : $item['name'],
                'type'      => $item['type'],
                'size'      => $item['size'],
            ];
        })->values()->all();

//        return collect($photos)->map(function ( $item ) {
//            return [
//                'id'        => $item['id'],
//                'name'      => basename(filter_var($item['name'], FILTER_VALIDATE_URL) === false ? Storage::url($item['name']) : $item['name']),
//                'thumbnail' => filter_var($item['thumbnail'], FILTER_VALIDATE_URL) === false ? Storage::url( Storage::exists($item['thumbnail']) ? $item['thumbnail'] : $item['name']) : (Storage::exists($item['thumbnail']) ? $item['thumbnail'] : $item['name']),
//                'url'       => filter_var($item['name'], FILTER_VALIDATE_URL) === false ? Storage::url($item['name']) : $item['name'],
//                'type'      => $item['type'],
//                'size'      => $item['size'],
//            ];
//        })->values()->all();
    }


    public function getVehicleIdsProperty( $vehicles ): array
    {
        return collect($vehicles)->pluck('id')->toArray();
    }

    public function getDxbInvProperty( $value )
    {
        if ( empty($value) || !Storage::exists($value) ) {
            return null;
        }

//        if ( empty($value) ) {
//            return null;
//        }

        return Storage::url($value);
    }

    public function getHybridExistProperty( $vehicles ): string
    {
         $is_exits =  collect($vehicles)->where('hybrid', 1 )->first();
         if($is_exits) {
             return 'Yes';
         }
         return 'No';
    }

    public function getDiffDaysProperty( $created_at ): string
    {
         if($created_at) {
             return \Carbon\Carbon::parse($created_at)->diffInDays();
         }
         return '';
    }

}
