<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerDocument extends Model
{
    protected $appends = ['type', 'size', 'name'];

    protected $fillable = [
        'file',
        'customer_user_id',
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d h:i:s',
        'updated_at' => 'datetime:Y-m-d h:i:s',
    ];

    public function getTypeAttribute()
    {
        return pathinfo($this->file, PATHINFO_EXTENSION);
    }

    public function getSizeAttribute()
    {
        //TODO:: need to get file size here
        return null;
    }

    public function getNameAttribute()
    {
        return  basename($this->file);
    }
}
