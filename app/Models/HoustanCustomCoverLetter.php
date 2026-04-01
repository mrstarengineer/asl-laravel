<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HoustanCustomCoverLetter extends Model
{
    protected $fillable = [
        'export_id',
        'vehicle_location',
        'exporter_id',
        'exporter_type_issuer',
        'transportation_value',
        'exporter_dob',
        'ultimate_consignee_dob',
        'consignee',
        'notify_party',
        'menifest_consignee',
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d h:i:s',
        'updated_at' => 'datetime:Y-m-d h:i:s',
    ];

    public function consignee_item ()
    {
        return $this->belongsTo( Consignee::class, 'consignee' );
    }

    public function notify_party_item ()
    {
        return $this->belongsTo( Consignee::class, 'notify_party' );
    }

    public function menifest_consignee_item ()
    {
        return $this->belongsTo( Consignee::class, 'menifest_consignee' );
    }
}
