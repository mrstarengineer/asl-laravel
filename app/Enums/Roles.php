<?php
namespace App\Enums;

abstract class Roles
{
    const MASTER_ADMIN = 0;
    const SUPER_ADMIN = 1;
    const LOCATION_ADMIN = 2;
    const CUSTOMER = 3;
    const EMPLOYEE = 4;
    const ACCOUNT = 5;
    const ADMIN = 6;
    const LOCATION_VIEW_ADMIN = 7;
    const LOCATION_RESTRICTED_ADMIN = 8;
}
