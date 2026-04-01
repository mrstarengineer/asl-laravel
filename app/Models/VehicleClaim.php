<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class VehicleClaim extends Model
{
    protected $fillable = [
        'vehicle_id',
        'export_id',
        'customer_user_id',
        'approved_by',
        'remarks',
        'claim_amount',
        'approved_amount',
        'approved_date',
        'create_date',
        'claim_status',
        'admin_remarks',
        'vehicle_part',
        'other_parts',
        'cust_view',
        'admin_view',
        'lot_number',
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d h:i:s',
        'updated_at' => 'datetime:Y-m-d h:i:s',
    ];

    protected $appends = [
        'damaged_part',
    ];

    public function getDamagedPartAttribute ()
    {
        return $this->vehicle_part ? Arr::get( trans( 'vehicle_claim.damage_parts' ), $this->vehicle_part, '' ) : '';
    }

    public function vehicle ()
    {
        return $this->belongsTo( Vehicle::class );
    }

    public function export ()
    {
        return $this->belongsTo( Export::class );
    }

    public function customer ()
    {
        return $this->hasOne( Customer::class, 'user_id', 'customer_user_id' );
    }

    public function customer_photos ()
    {
        return $this->hasMany( ClaimImage::class, 'claim_id' )->where( 'type', 1 );
    }

    public function admin_photos ()
    {
        return $this->hasMany( ClaimImage::class, 'claim_id' )->where( 'type', 2 );
    }
}
