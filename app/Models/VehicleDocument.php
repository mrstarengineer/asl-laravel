<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleDocument extends Model
{
    protected $fillable = [
        'name',
        'created_by',
        'updated_by',
        'vehicle_id',
        'invoice_id',
        'doc_type',
    ];

    protected $appends = ['type', 'size'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d h:i:s',
        'updated_at' => 'datetime:Y-m-d h:i:s',
    ];

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
