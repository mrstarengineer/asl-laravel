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

    'modules' => [
        [
            'module'      => 'customers',
            'label'       => 'Customer',
            'permissions' => [
                [ 'identifier' => 'customers.index', 'label' => 'Customer Index', 'has_access' => false, ],
                [ 'identifier' => 'customers.view', 'label' => 'Customer Detail', 'has_access' => false, ],
                [ 'identifier' => 'customers.store', 'label' => 'Customer Create', 'has_access' => false, ],
                [ 'identifier' => 'customers.update', 'label' => 'Customer Update', 'has_access' => false, ],
                [ 'identifier' => 'customers.destroy', 'label' => 'Customer Delete', 'has_access' => false, ],
            ],
        ],
        [
            'module'      => 'consignees',
            'label'       => 'Consignee',
            'permissions' => [
                [ 'identifier' => 'consignees.index', 'label' => 'Consignee Index', 'has_access' => false, ],
                [ 'identifier' => 'consignees.view', 'label' => 'Consignee Detail', 'has_access' => false, ],
                [ 'identifier' => 'consignees.store', 'label' => 'Consignee Create', 'has_access' => false, ],
                [ 'identifier' => 'consignees.update', 'label' => 'Consignee Update', 'has_access' => false, ],
                [ 'identifier' => 'consignees.destroy', 'label' => 'Consignee Delete', 'has_access' => false, ],
            ],
        ],
        [
            'module'      => 'vehicles',
            'label'       => 'Vehicle',
            'permissions' => [
                [ 'identifier' => 'vehicles.index', 'label' => 'Vehicle Index', 'has_access' => false, ],
                [ 'identifier' => 'vehicles.view', 'label' => 'Vehicle Detail', 'has_access' => false, ],
                [ 'identifier' => 'vehicles.store', 'label' => 'Vehicle Create', 'has_access' => false, ],
                [ 'identifier' => 'vehicles.update', 'label' => 'Vehicle Update', 'has_access' => false, ],
                [ 'identifier' => 'vehicles.destroy', 'label' => 'Vehicle Delete', 'has_access' => false, ],
            ],
        ],
        [
            'module'      => 'containers',
            'label'       => 'Container',
            'permissions' => [
                [ 'identifier' => 'containers.index', 'label' => 'Container Index', 'has_access' => false, ],
                [ 'identifier' => 'containers.view', 'label' => 'Container Detail', 'has_access' => false, ],
                [ 'identifier' => 'containers.store', 'label' => 'Container Create', 'has_access' => false, ],
                [ 'identifier' => 'containers.update', 'label' => 'Container Update', 'has_access' => false, ],
                [ 'identifier' => 'containers.destroy', 'label' => 'Container Delete', 'has_access' => false, ],
            ],
        ],
        [
            'module'      => 'exports',
            'label'       => 'Export',
            'permissions' => [
                [ 'identifier' => 'exports.index', 'label' => 'Export Index', 'has_access' => false, ],
                [ 'identifier' => 'exports.view', 'label' => 'Export Detail', 'has_access' => false, ],
                [ 'identifier' => 'exports.store', 'label' => 'Export Create', 'has_access' => false, ],
                [ 'identifier' => 'exports.update', 'label' => 'Export Update', 'has_access' => false, ],
                [ 'identifier' => 'exports.destroy', 'label' => 'Export Delete', 'has_access' => false, ],
            ],
        ],
        [
            'module'      => 'prices',
            'label'       => 'Price',
            'permissions' => [
                [ 'identifier' => 'prices.index', 'label' => 'Price Index', 'has_access' => false, ],
                [ 'identifier' => 'prices.view', 'label' => 'Price Detail', 'has_access' => false, ],
                [ 'identifier' => 'prices.store', 'label' => 'Price Create', 'has_access' => false, ],
                [ 'identifier' => 'prices.update', 'label' => 'Price Update', 'has_access' => false, ],
                [ 'identifier' => 'prices.destroy', 'label' => 'Price Delete', 'has_access' => false, ],
            ],
        ],
        [
            'module'      => 'reports',
            'label'       => 'Report',
            'permissions' => [
                [ 'identifier' => 'reports.vehicle-report', 'label' => 'Vehicle Report', 'has_access' => false, ],
                [ 'identifier' => 'reports.container-report', 'label' => 'Container Report', 'has_access' => false, ],
                [ 'identifier' => 'reports.customer-report', 'label' => 'Customer Report', 'has_access' => false, ],
                [ 'identifier' => 'reports.customer-record', 'label' => 'Customer Record Report', 'has_access' => false, ],
                [ 'identifier' => 'reports.customer-record', 'label' => 'Customer Invoice Report', 'has_access' => false, ],
                [ 'identifier' => 'reports.customer-title-status', 'label' => 'Customer Title Status', 'has_access' => false, ],
                [ 'identifier' => 'reports.customer-management', 'label' => 'Customer Management Report', 'has_access' => false, ],
            ],
        ],
    ],

];
