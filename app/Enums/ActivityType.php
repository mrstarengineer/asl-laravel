<?php

namespace App\Enums;

abstract class ActivityType
{
    const CREATE = 'create';
    const UPDATE = 'update';
    const DELETE = 'delete';
    const FETCH  = 'fetch';
}
