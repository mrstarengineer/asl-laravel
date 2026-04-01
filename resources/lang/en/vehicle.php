<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Vehicles Language Lines
    |--------------------------------------------------------------------------
    |
    */

    'statuses'   => [
        \App\Enums\VehicleStatus::ON_HAND     => 'ON HAND',
        \App\Enums\VehicleStatus::MANIFEST    => 'MANIFEST',
        \App\Enums\VehicleStatus::ON_THE_WAY  => 'ON THE WAY',
        \App\Enums\VehicleStatus::SHIPPED     => 'SHIPPED',
        \App\Enums\VehicleStatus::PICKED_UP   => 'PICKED UP',
        \App\Enums\VehicleStatus::ARRIVED     => 'ARRIVED',
        \App\Enums\VehicleStatus::HANDED_OVER => 'HANDED OVER',
    ],
    'title_type' => [
        0 => 'No TITLE',
        1 => 'EXPORTABLE',
        2 => 'PENDING',
        3 => 'BOS',
        4 => 'LIEN',
        5 => 'MV907',
        6 => 'REJECTED',
    ],
    'photos_types' => [
        \App\Enums\VehiclePhotoType::VEHICLE_PHOTO => 'vehicle_photos',
        \App\Enums\VehiclePhotoType::AUCTION_PHOTO => 'auction_photos',
        \App\Enums\VehiclePhotoType::PICKUP_PHOTO  => 'pickup_photos',
        \App\Enums\VehiclePhotoType::ARRIVE_PHOTO  => 'arrived_photos',
    ],
];
