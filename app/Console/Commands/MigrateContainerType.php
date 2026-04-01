<?php

namespace App\Console\Commands;

use App\Enums\ContainerType;
use App\Enums\SyncHistoryType;
use App\Models\Export;
use App\Models\SyncHistory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrateContainerType extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:container_type';

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
        $syncHistory = SyncHistory::where( 'history_type', SyncHistoryType::CONTAINER_TYPE )->first();

        DB::table('vehicles')
            ->join('vehicle_exports', 'vehicles.id', '=', 'vehicle_exports.vehicle_id')
            ->join('exports', 'exports.id', '=', 'vehicle_exports.export_id')
            ->update([ 'vehicles.export_id' => DB::raw("`exports`.`id`") ]);

        foreach (Export::whereHas('vehicles')->where( 'id', '>', $syncHistory->ref_id )->with('vehicles')->cursor() as $export) {
            if ( $export->vehicles->count() === $export->vehicles->where('customer_user_id', optional($export->vehicles->first())->customer_user_id)->count() ) {
                $export->update( [ 'is_full_container' => ContainerType::FULL_CONTAINER ] );
                $this->info( 'Full Container export id:  ' . $export->id );
            } else {
                $this->info( 'MIX Container export id:  ' . $export->id );
            }
        }

        if ( ! empty( $export ) && $syncHistory ) {
            $syncHistory->update( [ 'ref_id' => $export->id ] );
        }
    }
}
