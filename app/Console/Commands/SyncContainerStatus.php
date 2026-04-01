<?php

namespace App\Console\Commands;

use App\Enums\VehicleStatus;
use App\Models\Export;
use Illuminate\Console\Command;

class SyncContainerStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:container_status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Container Status';

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
     * @return void
     */
    public function handle ()
    {
        foreach ( Export::whereNotNull( 'eta' )->whereRaw( 'eta > export_date' )->whereDate( 'eta', '<=', date( 'Y-m-d' ) )->where( 'status', '<', VehicleStatus::ARRIVED )->cursor() as $export ) {
            $export->update( [ 'status' => VehicleStatus::ARRIVED, 'updated_at' => date( 'Y-m-d h:i:s') ] );
        }
    }
}
