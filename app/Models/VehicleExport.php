<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleExport extends Model
{
    protected $fillable = [
        'vehicle_id',
        'export_id',
        'customer_user_id',
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d h:i:s',
        'updated_at' => 'datetime:Y-m-d h:i:s',
    ];

    public function export()
    {
        return $this->belongsTo(Export::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}
