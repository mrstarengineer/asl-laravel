<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExportImage extends Model
{
    use SoftDeletes;

    protected $appends = ['type', 'size'];

    protected $fillable = [
        'name',
        'thumbnail',
        'export_id',
        'baseurl',
        'type',
    ];

    public function export()
    {
        return $this->belongsTo(Export::class);
    }

    public function getTypeAttribute()
    {
        return pathinfo($this->name, PATHINFO_EXTENSION);
    }

    public function getSizeAttribute()
    {
        //TODO:: need to get file size here
        return null;
    }
}
