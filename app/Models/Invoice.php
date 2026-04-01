<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $appends = [
        'upload_invoice_type',
        'upload_invoice_size',
        'upload_invoice_name',
        'clearance_invoice_type',
        'clearance_invoice_size',
        'clearance_invoice_name',
    ];

    protected $fillable = [
        'version_id',
        'export_id',
        'customer_user_id',
        'consignee_id',
        'total_amount',
        'paid_amount',
        'export_invoice',
        'note',
        'adjustment_damaged',
        'adjustment_storage',
        'adjustment_discount',
        'adjustment_other',
        'currency',
        'discount',
        'before_discount',
        'upload_invoice',
        'seen_by_customer',
        'clearance_invoice',
        'note2',
        'created_by',
        'updated_by',
        'deleted_at',
        'documents_migrated',
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d h:i:s',
        'updated_at' => 'datetime:Y-m-d h:i:s',
    ];

	public function customer()
	{
		return $this->hasOne(Customer::class,'user_id','customer_user_id');
	}

    public function export ()
    {
        return $this->belongsTo(Export::class);
	}

    public function documents ()
    {
        return $this->hasMany(VehicleDocument::class, 'invoice_id', 'id');
    }

    public function getUploadInvoiceTypeAttribute()
    {
        return pathinfo($this->upload_invoice, PATHINFO_EXTENSION);
    }

    public function getUploadInvoiceSizeAttribute()
    {
        //TODO:: need to get upload_invoice size here
        return null;
    }

    public function getUploadInvoiceNameAttribute()
    {
        return  basename($this->upload_invoice);
    }

    public function getClearanceInvoiceTypeAttribute()
    {
        return pathinfo($this->clearance_invoice, PATHINFO_EXTENSION);
    }

    public function getClearanceInvoiceSizeAttribute()
    {
        //TODO:: need to get clearance_invoice size here
        return null;
    }

    public function getClearanceInvoiceNameAttribute()
    {
        return  basename($this->clearance_invoice);
    }

    protected static function boot()
    {
        parent::boot();

        Invoice::updating( function ( $model ) {
            $model->version_id = $model->version_id + 1;
        } );
    }
}
