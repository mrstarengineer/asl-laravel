<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $casts = [
        'logs'         => 'array',
        'request_data' => 'array',
        'created_at'   => 'datetime:M d, Y h:i A',
        'updated_at'   => 'datetime:Y-m-d h:i:s',
    ];

    protected $fillable = [
        'user_id',
        'model_id',
        'title',
        'type',
        'logs',
        'request_data',
        'platform',
    ];

    public function user()
    {
        return $this->belongsTo( User::class );
    }
}
