<?php

namespace App\Models;

use App\Enums\ExportPhotoType;
use App\Enums\Roles;
use App\Enums\VehicleStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Export extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'version_id',
        'export_date',
        'loading_date',
        'broker_name',
        'booking_number',
        'eta',
        'ar_number',
        'xtn_number',
        'seal_number',
        'container_number',
        'cutt_off',
        'vessel',
        'voyage',
        'terminal',
        'streamship_line',
        'destination',
        'itn',
        'contact_details',
        'special_instruction',
        'container_type',
        'port_of_loading',
        'port_of_discharge',
        'export_invoice',
        'bol_note',
        'customer_user_id',
        'shipper_id',
        'bol_remarks',
        'oti_number',
        'notes_status',
        'note',
        'handed_over_date',
        'is_full_container',
        'created_by',
        'updated_by',
        'deleted_at',
        'documents_migrated',
        'photos_migrated',
    ];

    protected $casts = [
        'created_at'   => 'date:Y-m-d',
        'loading_date' => 'date:Y-m-d',
        'updated_at'   => 'datetime:Y-m-d h:i:s',
    ];

    public function vehicles()
    {
        if ( optional( auth()->user() )->role == Roles::CUSTOMER ) {
            return $this->hasMany( Vehicle::class )->where( 'customer_user_id', auth()->user()->id )->orderBy( 'id', 'desc' );
        } elseif ( request()->get( 'customer_user_id' ) ) {
            return $this->hasMany( Vehicle::class )->where( 'customer_user_id', request()->get( 'customer_user_id' ) )->orderBy( 'id', 'desc' );
        }

        return $this->hasMany( Vehicle::class )->orderBy( 'id', 'desc' );
    }

    public function all_vehicles()
    {
        return $this->hasMany( Vehicle::class )->orderBy( 'id', 'desc' );
    }

    public function vehicle_exports()
    {
        return $this->hasMany( VehicleExport::class );
    }

    public function export_images()
    {
        return $this->hasMany( ExportImage::class )->where( 'type', ExportPhotoType::EXPORT_PHOTO );
    }

    public function empty_container_photos()
    {
        return $this->hasMany( ExportImage::class )->where( 'type', ExportPhotoType::EMPTY_CONTAINER_PHOTO );
    }

    public function loading_photos()
    {
        return $this->hasMany( ExportImage::class )->where( 'type', ExportPhotoType::LOADING_PHOTO );
    }

    public function loaded_photos()
    {
        return $this->hasMany( ExportImage::class )->where( 'type', ExportPhotoType::LOADED_PHOTO );
    }

    public function customer()
    {
        return $this->belongsTo( Customer::class, 'customer_user_id', 'user_id' );
    }

    public function exporter()
    {
        return $this->belongsTo( Customer::class, 'shipper_id', 'user_id' );
    }

    public function invoice()
    {
        if ( optional( auth()->user() )->role == Roles::CUSTOMER ) {
            return $this->hasOne( Invoice::class )->where( 'customer_user_id', auth()->user()->id );
        }

        return $this->hasOne( Invoice::class );
    }

    public function houstan_custom_cover_letter()
    {
        return $this->hasOne( HoustanCustomCoverLetter::class );
    }

    public function dock_receipt()
    {
        return $this->hasOne( DockReceipt::class );
    }

    protected static function boot()
    {
        parent::boot();
        Export::saving( function ( $model ) {
            if ( $model->export_date && $model->eta && $model->eta <= date( 'Y-m-d' ) && $model->eta > $model->export_date ) {
                $model->status = VehicleStatus::ARRIVED;
            } elseif ( $model->container_number && $model->booking_number ) {
                $model->status = VehicleStatus::SHIPPED;
            } else {
                $model->status = VehicleStatus::MANIFEST;
            }
        } );

        Export::updating( function ( $model ) {
            $model->version_id = $model->version_id + 1;
            if ( $model->status == VehicleStatus::ARRIVED && !empty( $model->handed_over_date ) ) {
                $model->status = VehicleStatus::HANDED_OVER;
            }
        } );

        Export::updated( function ( $model ) {
            $model->version_id = $model->version_id + 1;
            Vehicle::where( 'export_id', $model->id )
                ->where( 'status', '<', $model->status )
                ->update( [ 'status' => $model->status ] );
        } );
    }
}
