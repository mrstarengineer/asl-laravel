<?php

namespace App\Jobs;

use App\Enums\SyncHistoryType;
use App\Services\MediaMigrate\MediaMigrateService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class FileMigratorJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 180;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 1;

    private $type;
    private $refId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($migrationType, $refId)
    {
        $this->type = $migrationType;
        $this->refId = $refId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        switch ( $this->type ) {
            case SyncHistoryType::VEHICLE_PHOTO:
                app(MediaMigrateService::class )->migrateVehicleImage( $this->refId );
                break;

            case SyncHistoryType::VEHICLE_DOCUMENT:
                app(MediaMigrateService::class )->migrateVehicleDocument( $this->refId );
                break;

            case SyncHistoryType::EXPORT_PHOTO:
                app(MediaMigrateService::class )->migrateContainerPhoto( $this->refId );
                break;

            case SyncHistoryType::EXPORT_DOCUMENT:
                app(MediaMigrateService::class )->migrateContainerDocument( $this->refId );
                break;

            case SyncHistoryType::CUSTOMER_DOCUMENT:
                app(MediaMigrateService::class )->migrateCustomerDocument( $this->refId );
                break;

            case SyncHistoryType::INVOICE:
                app(MediaMigrateService::class )->migrateInvoiceDocuments( $this->refId );
                break;

            case SyncHistoryType::VEHICLE_THUMBNAIL_PHOTO:
                app(MediaMigrateService::class )->migrateVehicleThumbnailImages( $this->refId );
                break;

            case SyncHistoryType::EXPORT_THUMBNAIL_PHOTO:
                app(MediaMigrateService::class )->migrateContainerThumbnailImages( $this->refId );
                break;
        }
    }
}
