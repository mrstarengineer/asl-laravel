<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'subject',
        'message',
        'is_read',
        'status',
        'created_by',
        'updated_by',
        'user_id',
        'expire_date',
        'only_notify',
    ];

    protected $casts = [
        'created_at' => 'datetime:d M Y h:i A',
        'updated_at' => 'datetime:Y-m-d h:i:s',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
