<?php

namespace App\Console\Commands;

use App\Enums\SyncHistoryType;
use App\Jobs\FileMigratorJob;
use App\Models\Invoice;
use App\Models\SyncHistory;
use Illuminate\Console\Command;

class InvoiceDocumentMigration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:invoice-documents';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate Invoice Document';

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
        $syncHistory = SyncHistory::where( 'history_type', SyncHistoryType::INVOICE )->first();

        foreach ( Invoice::where( 'id', '>', $syncHistory->ref_id )->cursor() as $invoice ) {
            FileMigratorJob::dispatch( SyncHistoryType::INVOICE, $invoice->id );
        }

        if ( ! empty( $invoice ) && $syncHistory ) {
            $syncHistory->update( [ 'ref_id' => $invoice->id ] );
        }

        return 0;
    }

}
