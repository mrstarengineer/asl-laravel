<?php

namespace App\Console\Commands;

use App\Enums\MediaMigratorType;
use App\Enums\SyncHistoryType;
use App\Jobs\FileMigratorJob;
use App\Jobs\MediaMigratorJob;
use App\Models\Customer;
use App\Models\CustomerDocument;
use App\Models\SyncHistory;
use App\Models\VehicleImage;
use Illuminate\Console\Command;

class CustomerDocumentMigration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:customer-documents';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate Customer Document';

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
        $syncHistory = SyncHistory::where( 'history_type', SyncHistoryType::CUSTOMER_DOCUMENT )->first();

        foreach ( CustomerDocument::where( 'id', '>', $syncHistory->ref_id )->cursor() as $document ) {
            FileMigratorJob::dispatch( SyncHistoryType::CUSTOMER_DOCUMENT, $document->id );
        }

        if ( ! empty( $document ) && $syncHistory ) {
            $syncHistory->update( [ 'ref_id' => $document->id ] );
        }

        return 0;
    }


}
