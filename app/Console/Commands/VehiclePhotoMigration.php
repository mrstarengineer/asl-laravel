<?php

namespace App\Console\Commands;

use App\Enums\SyncHistoryType;
use App\Jobs\FileMigratorJob;
use App\Models\SyncHistory;
use App\Models\VehicleImage;
use Illuminate\Console\Command;

class VehiclePhotoMigration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:vehicle-photos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate vehicle photos';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct ()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle ()
    {
        $syncHistory = SyncHistory::where( 'history_type', SyncHistoryType::VEHICLE_PHOTO )->first();

        foreach ( VehicleImage::where( 'id', '>', $syncHistory->ref_id )->cursor() as $image ) {
            FileMigratorJob::dispatch( SyncHistoryType::VEHICLE_PHOTO, $image->id );
        }

        if ( ! empty( $image ) && $syncHistory ) {
            $syncHistory->update( [ 'ref_id' => $image->id ] );
        }

        return 0;
    }

}
