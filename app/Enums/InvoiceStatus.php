<?php
namespace App\Enums;

abstract class InvoiceStatus
{
    const UNPAID = 1;
    const PAID = 2;
    const PARTIALLY_PAID = 3;
}
