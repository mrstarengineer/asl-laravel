<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Yard extends Model
{
    use HasFactory;

    protected $fillable = [
        'location_id',
        'name',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d h:i:s',
        'updated_at' => 'datetime:Y-m-d h:i:s',
    ];

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    protected static function boot()
    {
        parent::boot();

        Export::created(function ( $model ) {
            $model->created_by = auth()->user()->id;
        });

        Export::updated(function ( $model ) {
            $model->updated_by = auth()->user()->id;
        });
    }

}
