<?php

namespace App\Console\Commands;

use App\Enums\MediaMigratorType;
use App\Jobs\MediaMigratorJob;
use App\Models\Vehicle;
use Illuminate\Console\Command;

class VehicleImageMigration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:vehicle-images';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate vehicle images';

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
        foreach ( Vehicle::where('created_at', '>=', '2021-01-01')->where(function ( $q) {
            $q->where( 'documents_migrated', 0 )->orWhere( 'photos_migrated', 0 );
        })->orderBy( 'id', 'DESC' )->cursor() as $vehicle ) {
            MediaMigratorJob::dispatch( MediaMigratorType::VEHICLE, $vehicle->id );
        }

        return 0;
    }

}
