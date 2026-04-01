<?php

namespace App\Console\Commands;

use App\Models\CustomerDocument;
use App\Models\Export;
use App\Models\ExportImage;
use App\Models\Invoice;
use App\Models\SyncHistory;
use App\Models\VehicleDocument;
use App\Models\VehicleImage;
use Illuminate\Console\Command;

class CleanDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clean:db';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear Testing data from database tables';

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
        /**
        $syncHistories = SyncHistory::pluck( 'ref_id', 'history_type' )->toArray();
        if ( ! empty( $syncHistories[ 'vehicle_photo' ] ) ) {
            VehicleImage::where( 'id', '>', $syncHistories[ 'vehicle_photo' ] )->delete();
        }
        if ( ! empty( $syncHistories[ 'vehicle_document' ] ) ) {
            VehicleDocument::where( 'id', '>', $syncHistories[ 'vehicle_document' ] )->delete();
        }
        if ( ! empty( $syncHistories[ 'export_photo' ] ) ) {
            ExportImage::where( 'id', '>', $syncHistories['export_photo'])->delete();
        }
        if ( ! empty( $syncHistories[ 'export_document' ] ) ) {
            Export::where( 'id', '>', $syncHistories['export_document'])->delete();
        }
        if ( ! empty( $syncHistories[ 'invoice' ] ) ) {
            Invoice::where( 'id', '>', $syncHistories['invoice'])->delete();
        }
        if ( ! empty( $syncHistories[ 'customer_document' ] ) ) {
            CustomerDocument::where( 'id', '>', $syncHistories['customer_document'])->delete();
        }**/
    }
}
