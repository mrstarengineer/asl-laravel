<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Vehicles Language Lines
    |--------------------------------------------------------------------------
    |
    */

    'statuses'           => [
        \App\Enums\VehicleStatus::MANIFEST    => 'MANIFEST',
        \App\Enums\VehicleStatus::SHIPPED     => 'SHIPPED',
        \App\Enums\VehicleStatus::ARRIVED     => 'ARRIVED',
        \App\Enums\VehicleStatus::HANDED_OVER => 'HANDED OVER',
    ],
    'container_types'    => [
        1 => "1 X 20'HC DRY VAN",
        2 => "1 X 45'HC DRY VAN",
        3 => "1 X 40'HC DRY VAN",
    ],
    'port_of_loadings'   => [
        1 => "NEW JERSEY, NWJ",
        2 => "HOUSTON, TX",
        3 => "LOS ANGELES, CA",
        4 => "NEWARK, NJ",
        5 => "SAVANNAH, GA",
        6 => "HOUSTON, TEXAS",
        7 => "HONOLULU, HI",
        8 => "BALTIMORE, MD",
        9 => "SHANGHAI ,CNSHA",
    ],
    'port_of_discharges' => [
        1  => "JEBEL ALI, UAE",
        2  => "AQABA, JORDAN",
        3  => "KARACHI, PAKISTAN",
        4  => "SOHAR, OMAN",
        5  => "UMM QASR, IRAQ",
        6  => "MERSIN, TURKEY",
        7  => "CAMBODIA",
        8  => "BAHRAIN",
        9  => "MUSCAT,OMAN",
        10 => "FREETOWN, SIERRA LEONE",
        11 => "TEMA, GHANA",
        12 => "VIETMAN",
        13 => "BUSAN, KOREA",
        14 => "GERMANY",
        15 => "MISURATA, LIBYA",
        16 => "NIGERIA",
        17 => "DENMARK",
        18 => "BENGHAZI, LIBYA",
        19 => "MYANMAR",
        20 => "SALALAH, OMAN",
        21 => "LIBYA - ALKHOMS",
        22 => "BEIRUT, LEBANON",
    ],
    'photos_types'       => [
        \App\Enums\ExportPhotoType::EXPORT_PHOTO          => 'export_photos',
        \App\Enums\ExportPhotoType::EMPTY_CONTAINER_PHOTO => 'empty_container_photos',
        \App\Enums\ExportPhotoType::LOADING_PHOTO         => 'loading_photos',
        \App\Enums\ExportPhotoType::LOADED_PHOTO          => 'loaded_photos',
    ],
];
