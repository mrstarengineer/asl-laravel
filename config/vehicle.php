<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Vehicles
    |--------------------------------------------------------------------------
    |
    | Vehicle related configuration will be here.
    |
    */

    'statuses' => [
	    ['status' => \App\Enums\VehicleStatus::PICKED_UP, 'label' => 'Picked Up', 'color' => '#2c51d9','logo' => '/images/picked_up.png'],
        ['status' => \App\Enums\VehicleStatus::ON_THE_WAY, 'label' => 'Car on the way', 'color' => '#ffe700','logo' => '/images/car_on_the_way.png'],
        ['status' => \App\Enums\VehicleStatus::ON_HAND, 'label' => 'Car On Hand', 'color' => '#f46a69','logo' => '/images/caron_hand.png'],
        ['status' => \App\Enums\VehicleStatus::MANIFEST, 'label' => 'Manifest', 'color' => '#2d99ff','logo' => '/images/manifest3.png'],
        ['status' => \App\Enums\VehicleStatus::SHIPPED, 'label' => 'Shipped', 'color' => '#826af9' ,'logo' => '/images/shipped_cars.png'],
        ['status' => \App\Enums\VehicleStatus::ARRIVED, 'label' => 'Arrived', 'color' => '#2cd9c5','logo' => '/images/shipped_cars.png'],
        ['status' => \App\Enums\VehicleStatus::HANDED_OVER, 'label' => 'Handed Over', 'color' => '#2cd9c5','logo' => '/images/handed_over.png'],
        ['status' => '', 'label' => 'All Vehicles', 'color' => '#','logo' => '/images/all_vehicles.png'],
    ],

];
