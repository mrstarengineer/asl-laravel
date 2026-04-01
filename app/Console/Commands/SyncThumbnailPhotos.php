<?php

namespace App\Console\Commands;

use App\Models\Customer;
use App\Models\Export;
use App\Models\ExportImage;
use App\Models\Invoice;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleImage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SyncThumbnailPhotos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:thumbnail-photos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->syncVehicleThumbnailPhotos();
        $this->syncContainerThumbnailPhotos();

        return 0;
    }

    private function syncVehicleThumbnailPhotos () {
        foreach ( VehicleImage::where('id', '>', 1371347)->cursor() as $image ) {
            try {
                $img = \Intervention\Image\ImageManagerStatic::make(Storage::url($image->name))->fit(450);
                if ($img) {
                    $fileName = 'uploads/vehicles/images/' . $image->vehicle_id . '/thumb-' . basename( $image->name );
                    Storage::put( $fileName, $img->encode() );
                    $image->update( [ 'thumbnail' => $fileName ] );
                }
                $this->info( $image->id . ' image id vehicle photo migrated successfully.' );
            } catch ( \Exception $e ) {
                $this->error( $image->id . ' image id vehicle photo migrated failed' );
            }
        }
    }

    private function syncContainerThumbnailPhotos () {
        foreach( ExportImage::where('id', '>', 642111)->cursor() as $image ) {
            try {
                $img = \Intervention\Image\ImageManagerStatic::make(Storage::url($image->name))->fit(450);
                if ($img) {
                    $fileName = 'uploads/exports/images/' . $image->export_id . '/thumb-' . basename( $image->name );
                    Storage::put( $fileName, $img->encode() );
                    $image->update( [ 'thumbnail' => $fileName ] );
                }
                $this->info( $image->id . ' image id container photo migrated successfully.' );
            } catch ( \Exception $e ) {
                $this->error( $image->id . ' image id container photo migrated failed' );
            }
        }
    }
}
