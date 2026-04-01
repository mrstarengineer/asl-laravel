<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TowingRequest extends Model
{
    protected $fillable = [
        'condition',
        'damaged',
        'pictures',
        'towed',
        'title_received',
        'title_received_date',
        'title_number',
        'title_state',
        'towing_request_date',
        'pickup_date',
        'deliver_date',
        'note',
        'title_type',
    ];

    protected $casts = [
        'condition'      => 'string',
        'damaged'        => 'string',
        'pictures'       => 'string',
        'towed'          => 'string',
        'title_received' => 'string',
        'created_at'     => 'datetime:Y-m-d h:i:s',
        'updated_at'     => 'datetime:Y-m-d h:i:s',
    ];
}
