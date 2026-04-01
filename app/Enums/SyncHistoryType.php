<?php
namespace App\Enums;

abstract class SyncHistoryType
{
    const VEHICLE = 'vehicle';
    const VEHICLE_PHOTO = 'vehicle_photo';
    const VEHICLE_DOCUMENT = 'vehicle_document';

    const EXPORT = 'export';
    const EXPORT_PHOTO = 'export_photo';
    const EXPORT_DOCUMENT = 'export_document';

    const CUSTOMER_DOCUMENT = 'customer_document';
    const INVOICE = 'invoice';
    const CONTAINER_TYPE = 'container_type';

    const VEHICLE_THUMBNAIL_PHOTO = 'vehicle_thumbnail_photo';
    const EXPORT_THUMBNAIL_PHOTO  = 'export_thumbnail_photo';
}
