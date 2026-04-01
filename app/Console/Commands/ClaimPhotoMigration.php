<?php

namespace App\Console\Commands;

use App\Models\ClaimImage;
use App\Services\MediaMigrate\MediaMigrateService;
use Illuminate\Console\Command;

class ClaimPhotoMigration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:claim-photos';

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
        foreach ( ClaimImage::where( 'id', '>', 0 )->cursor() as $image ) {
            app( MediaMigrateService::class )->migrateClaimImage( $image->id );
        }

        return 0;
    }

}
