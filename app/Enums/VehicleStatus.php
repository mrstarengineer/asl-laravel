<?php
namespace App\Enums;

abstract class VehicleStatus
{
    const ON_HAND = 1;
    const MANIFEST = 2;
    const ON_THE_WAY = 3;
    const SHIPPED = 4;
    const PICKED_UP = 5;
    const ARRIVED = 6;
    const HANDED_OVER = 7;
}
