<?php

namespace App\Jobs;

use App\Enums\MediaMigratorType;
use App\Services\MediaMigrate\MediaMigrateService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class MediaMigratorJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 600;

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
        switch ($this->type) {
            case MediaMigratorType::CUSTOMER_DOCUMENT:
                app(MediaMigrateService::class)->migrateCustomerDocuments($this->refId);
                break;

            case MediaMigratorType::VEHICLE:
                app(MediaMigrateService::class)->migrateVehicleMediaFiles($this->refId);
                break;

            case MediaMigratorType::EXPORT:
                app(MediaMigrateService::class)->migrateExportMediaFiles($this->refId);
                break;

            case MediaMigratorType::INVOICE:
                app(MediaMigrateService::class)->migrateInvoiceDocuments($this->refId);
                break;
        }
    }
}
