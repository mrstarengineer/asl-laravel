<?php

namespace App\Models;

use App\Enums\VehicleDocumentType;
use App\Enums\VehiclePhotoType;
use App\Enums\VehicleStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'version_id',
        'hat_number',
        'vehicle_type',
        'year',
        'color',
        'model',
        'make',
        'vin',
        'weight',
        'weight_in_kg',
        'pieces',
        'value',
        'license_number',
        'towed_from',
        'lot_number',
        'towed_amount',
        'storage_amount',
        'status',
        'load_status',
        'check_number',
        'additional_charges',
        'location_id',
        'yard_id',
        'customer_user_id',
        'towing_request_id',
        'is_export',
        'title_amount',
        'container_number',
        'keys',
        'key_note',
        'notes_status',
        'prepared_by',
        'auction_at',
        'vcr',
        'note',
        'export_id',
        'handed_over_date',
        'created_by',
        'updated_by',
        'deleted_at',
        'photos_migrated',
        'documents_migrated',
        'hybrid',
    ];

    protected $appends = [
        'age',
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d h:i:s',
        'updated_at' => 'datetime:Y-m-d h:i:s',
    ];

    public function getAgeAttribute ()
    {
        $delivered = time();
        if ($this->towing_request && $this->towing_request->deliver_date && $this->status == VehicleStatus::ON_HAND) {
            $p = $this->towing_request->deliver_date;
        } else if($this->status == VehicleStatus::MANIFEST && data_get($this, 'export.created_at')) {
            $p = data_get($this, 'export.created_at');
        } else if($this->status == VehicleStatus::SHIPPED && data_get($this, 'export.export_date')) {
            $p = data_get($this, 'export.export_date');
        } else if($this->status == VehicleStatus::ARRIVED && data_get($this, 'export.export_date') && data_get($this, 'export.eta')) {
            $p = data_get($this, 'export.export_date');
            $delivered = strtotime( data_get($this, 'export.eta') );
        } else {
            if ($this->towing_request && $this->status == VehicleStatus::ON_THE_WAY) {
                $p = $this->towing_request->towing_request_date;
            } else {
                return 0;
            }
        }


        $pickedup = strtotime($p);
        $datediff = $delivered - $pickedup;

        return round($datediff / (60 * 60 * 24));
    }

    public function export ()
    {
        return $this->belongsTo( Export::class );
    }

    public function vehicle_export ()
    {
        return $this->hasOne( VehicleExport::class );
    }

    public function location ()
    {
        return $this->belongsTo( Location::class );
    }

    public function yard ()
    {
        return $this->belongsTo( Yard::class );
    }

    public function towing_request ()
    {
        return $this->belongsTo( TowingRequest::class );
    }

    public function vehicle_image ()
    {
        return $this->hasMany( VehicleImage::class )->where( 'type', VehiclePhotoType::VEHICLE_PHOTO );
    }

    public function auction_photos ()
    {
        return $this->hasMany( VehicleImage::class )->where( 'type', VehiclePhotoType::AUCTION_PHOTO );
    }

    public function pickup_photos ()
    {
        return $this->hasMany( VehicleImage::class )->where( 'type', VehiclePhotoType::PICKUP_PHOTO );
    }

    public function arrived_photos ()
    {
        return $this->hasMany( VehicleImage::class )->where( 'type', VehiclePhotoType::ARRIVE_PHOTO );
    }

    public function customer ()
    {
        return $this->hasOne( Customer::class, 'user_id', 'customer_user_id' );
    }

    public function vehicle_conditions ()
    {
        return $this->hasMany( VehicleCondition::class, 'vehicle_id', 'id' );
    }

    public function vehicle_features ()
    {
        return $this->hasMany( VehicleFeature::class, 'vehicle_id', 'id' );
    }

    public function vehicle_claims ()
    {
        return $this->hasMany( VehicleClaim::class, 'vehicle_id', 'id' );
    }

    public function vehicle_documents ()
    {
        return $this->hasMany( VehicleDocument::class, 'vehicle_id', 'id' )->where( 'doc_type', VehicleDocumentType::DOCUMENT );
    }

    public function invoice_photos ()
    {
        return $this->hasMany( VehicleDocument::class, 'vehicle_id', 'id' )->where( 'doc_type', VehicleDocumentType::INVOICE );
    }

    public function notes ()
    {
        return $this->hasMany( Note::class, 'vehicle_id', 'id' );
    }

    protected static function boot ()
    {
        parent::boot();
        Vehicle::creating( function ( $model ) {
            if ( empty( $model->status ) && ! empty( $model->deliver_date ) ) {
                $model->status = VehicleStatus::ON_HAND;
            } elseif ( empty( $model->status ) ) {
                $model->status = VehicleStatus::ON_THE_WAY;
            }
            $model->version_id = 1;
            if( empty($model->vcr) ) {
                $model->vcr = Vehicle::max('vcr') + 1;
            }
        } );

        Vehicle::updating( function ( $model ) {
            $model->version_id = $model->version_id + 1;
            if ( $model->status == VehicleStatus::ARRIVED && ! empty( $model->handed_over_date ) ) {
                $model->status = VehicleStatus::HANDED_OVER;
            } /*elseif ( ! empty( $model->deliver_date ) && empty( $model->getOriginal( 'deliver_date' ) ) ) {
                $model->status = VehicleStatus::ON_HAND;
            }*/
            if ( empty( $model->vcr ) ) {
                $model->vcr = Vehicle::max( 'vcr' ) + 1;
            }

            /* Vehicle unassigned from export */
            if( empty( $model->export_id ) && ! empty( $model->getOriginal( 'export_id' ) ) ) {
                $model->status = VehicleStatus::ON_HAND;
            }
        } );
    }
}
