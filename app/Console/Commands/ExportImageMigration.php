<?php

namespace App\Console\Commands;

use App\Enums\ExportPhotoType;
use App\Enums\MediaMigratorType;
use App\Jobs\MediaMigratorJob;
use App\Models\Export;
use App\Models\ExportImage;
use Illuminate\Console\Command;

class ExportImageMigration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:export-images';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate export images';

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
        foreach ( Export::where( 'created_at', '>=', '2021-01-01' )->where( function( $q ) {
            $q->where( 'documents_migrated', 0 )->OrWhere( 'photos_migrated', 0 );
        } )->orderBy( 'id', 'desc' )->cursor() as $export ) {
            MediaMigratorJob::dispatch( MediaMigratorType::EXPORT, $export->id );
        }

        ExportImage::whereNull( 'type' )->update( [ 'type' => ExportPhotoType::EXPORT_PHOTO ] );

        return 0;
    }

}
