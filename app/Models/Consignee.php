<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Consignee extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'version_id',
        'customer_user_id',
        'consignee_name',
        'consignee_address_1',
        'consignee_address_2',
        'city_id',
        'state_id',
        'country_id',
        'zip_code',
        'phone',
        'created_by',
        'updated_by',
        'deleted_at',
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d h:i:s',
        'updated_at' => 'datetime:Y-m-d h:i:s',
    ];

    public function customer ()
    {
        return $this->hasOne( Customer::class, 'user_id', 'customer_user_id' );
    }

    public function country ()
    {
        return $this->belongsTo( Country::class );
    }

    public function state ()
    {
        return $this->belongsTo( State::class );
    }

    public function city ()
    {
        return $this->belongsTo( City::class );
    }

    protected static function boot()
    {
        parent::boot();

        Consignee::updating( function ( $model ) {
            $model->version_id = $model->version_id + 1;
        } );
    }
}
