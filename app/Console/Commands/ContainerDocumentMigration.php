<?php

namespace App\Console\Commands;

use App\Enums\SyncHistoryType;
use App\Jobs\FileMigratorJob;
use App\Models\Export;
use App\Models\SyncHistory;
use Illuminate\Console\Command;

class ContainerDocumentMigration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:container-documents';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate container documents';

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
        $syncHistory = SyncHistory::where( 'history_type', SyncHistoryType::EXPORT_DOCUMENT )->first();

        foreach ( Export::where( 'id', '>', $syncHistory->ref_id )->cursor() as $document ) {
            FileMigratorJob::dispatch( SyncHistoryType::EXPORT_DOCUMENT, $document->id );
        }

        if ( ! empty( $document ) && $syncHistory ) {
            $syncHistory->update( [ 'ref_id' => $document->id ] );
        }

        return 0;
    }

}
