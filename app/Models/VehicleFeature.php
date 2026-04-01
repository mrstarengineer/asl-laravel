<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleFeature extends Model
{
    protected $fillable = [
        'vehicle_id',
        'features_id',
        'value',
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d h:i:s',
        'updated_at' => 'datetime:Y-m-d h:i:s',
    ];

    public function feature()
    {
        return $this->belongsTo(Feature::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}
