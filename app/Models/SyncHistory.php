<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SyncHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'history_type',
        'ref_id',
        'misc',
    ];
}
