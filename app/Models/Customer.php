<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'version_id',
        'user_id',
        'customer_name',
        'company_name',
        'phone',
        'phone_two',
        'address_line_1',
        'address_line_2',
        'city_id',
        'state_id',
        'country_id',
        'zip_code',
        'tax_id',
        'fax',
        'trn',
        'other_emails',
        'note',
        'legacy_customer_id',
        'loading_type',
        'created_by',
        'updated_by',
        'deleted_at',
    ];

    protected $casts = [
        'legacy_customer_id' => 'int',
        'created_at'         => 'datetime:Y-m-d h:i:s',
        'updated_at'         => 'datetime:Y-m-d h:i:s',
    ];

    public function user()
    {
        return $this->belongsTo( User::class );
    }

    public function vehicles()
    {
        return $this->hasMany( Vehicle::class, 'customer_user_id', 'user_id' );
    }

    public function customer_documents()
    {
        return $this->hasMany( CustomerDocument::class, 'customer_user_id', 'user_id' );
    }

    public function exports()
    {
        return $this->hasMany( Export::class, 'customer_user_id', 'user_id' );
    }

    public function consignees()
    {
        return $this->hasMany( Consignee::class, 'customer_user_id', 'user_id' );
    }

    public function invoices()
    {
        return $this->hasMany( Invoice::class, 'customer_user_id', 'user_id' );
    }

    public function country()
    {
        return $this->belongsTo( Country::class );
    }

    public function state()
    {
        return $this->belongsTo( State::class );
    }

    public function city()
    {
        return $this->belongsTo( City::class );
    }

    protected static function boot()
    {
        parent::boot();

        Customer::creating( function ( $model ) {
            if ( empty( $model->legacy_customer_id ) ) {
                $model->legacy_customer_id = ( Customer::max( 'legacy_customer_id' ) ?? 2022000 ) + 1;
            }
        } );

        Customer::updating( function ( $model ) {
            $model->version_id = $model->version_id + 1;
        } );
    }
}
