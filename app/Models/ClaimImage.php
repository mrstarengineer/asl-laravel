<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClaimImage extends Model
{
    protected $appends = ['type', 'size'];

    protected $fillable = [
        'image',
        'thumbnail',
        'claim_id',
        'type',
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d h:i:s',
        'updated_at' => 'datetime:Y-m-d h:i:s',
    ];

    public function vehicle_claim()
    {
        return $this->belongsTo(VehicleClaim::class, 'claim_id', 'id');
    }

    public function getTypeAttribute()
    {
        return pathinfo($this->image, PATHINFO_EXTENSION);
    }

    public function getSizeAttribute()
    {
        //TODO:: need to get file size here
        return null;
    }
}
