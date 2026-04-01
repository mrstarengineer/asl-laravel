<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    protected $fillable = [
        'description',
        'export_id',
        'vehicle_id',
        'image_url',
        'cust_view',
        'admin_view',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d h:i:s',
        'updated_at' => 'datetime:Y-m-d h:i:s',
    ];

    public function vehicle ()
    {
        return $this->belongsTo( Vehicle::class );
    }

    public function export ()
    {
        return $this->belongsTo( Export::class );
    }

    public function user ()
    {
        return $this->belongsTo( User::class, 'created_by' );
    }
}
