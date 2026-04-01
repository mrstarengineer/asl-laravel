<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'message',
        'sender_id',
        'model_id',
    ];

    protected $casts = [
        'created_at' => 'datetime:h:i A, d M',
    ];

    public function sender ()
    {
        return $this->belongsTo( User::class, 'sender_id' );
    }
}
