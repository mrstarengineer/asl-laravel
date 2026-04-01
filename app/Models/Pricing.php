<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pricing extends Model
{
    protected $fillable = [
        'upload_file',
        'month',
        'str_month',
        'pricing_type',
        'status',
        'description',
        'created_by',
        'updated_by',
    ];

    protected $appends = ['file_type', 'file_size', 'file_name'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d h:i:s',
        'updated_at' => 'datetime:Y-m-d h:i:s',
    ];

    public function getFileTypeAttribute()
    {
        return pathinfo($this->upload_file, PATHINFO_EXTENSION);
    }

    public function getFileSizeAttribute()
    {
        //TODO:: need to get file size here
        return null;
    }

    public function getFileNameAttribute()
    {
        return  basename($this->upload_file);
    }
}
