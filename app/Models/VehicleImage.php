<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleImage extends Model
{
    protected $appends = ['type', 'size'];

    protected $fillable = [
        'name',
        'thumbnail',
        'vehicle_id',
        'created_at',
        'updated_at',
        'created_by',
        'updated_by',
        'baseurl',
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d h:i:s',
        'updated_at' => 'datetime:Y-m-d h:i:s',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function getTypeAttribute()
    {
        return pathinfo($this->name, PATHINFO_EXTENSION);
    }

    public function getSizeAttribute()
    {
        //TODO:: need to get file size here
        return null;
    }
}
