<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    protected $fillable = [
        'customer_user_id',
        'status',
        'message',
        'note',
        'read_by_admin',
    ];

    protected $casts = [
        'created_at' => 'datetime:M d, Y h:i A',
    ];

    public function customer ()
    {
        return $this->hasOne( Customer::class, 'user_id', 'customer_user_id' );
    }
}
