<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleCondition extends Model
{
    protected $fillable = [
        'vehicle_id',
        'condition_id',
        'value',
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d h:i:s',
        'updated_at' => 'datetime:Y-m-d h:i:s',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function condition()
    {
        return $this->belongsTo(Condition::class);
    }
}
