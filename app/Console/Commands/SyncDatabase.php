<?php

namespace App\Console\Commands;

use App\Models\Vehicle;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SyncDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:db';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync data from ariana old database to new.';

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
        $config = config( 'data_migration' );
        foreach ( $config[ 'table_mapping' ] as $oldTale => $newTable ) {
            if ( empty( $config[ 'field_mapping' ][ $newTable[ 'table' ] ] ) ) {
                $this->error( 'Field mapping not found for table: ' . $newTable[ 'table' ] );
                continue;
            }

            $orderByColumn = Schema::Connection( 'amaya_db' )->getColumnListing( $oldTale )[ 0 ];
            $effected = 0;
            $query = DB::connection( 'amaya_db' )->table( $oldTale );

            // find latest item
            $row = DB::table( $newTable[ 'table' ] )->latest( $newTable[ 'unique_column' ] )->first();
            if ( $row ) {
                $query->where( $newTable[ 'unique_column' ], '>', $row->{$newTable[ 'unique_column' ]} );
            }

            $query->orderBy( $orderByColumn )->chunk( 50, function ( $items ) use ( $config, $newTable, &$effected, $oldTale ) {
                foreach ( $items as $item ) {
                    try {
                        $data = [];
                        foreach ( $config[ 'field_mapping' ][ $newTable[ 'table' ] ] as $col => $oldCol ) {
                            $data[ $col ] = $item->$oldCol === '0000-00-00' ? null : $item->$oldCol;
                        }
                        if ( data_get( $item, 'is_deleted', 0 ) && Schema::hasColumn( $newTable[ 'table' ], 'deleted_at' ) ) {
                            $data[ 'deleted_at' ] = now();
                        }
                        DB::table( $newTable[ 'table' ] )->updateOrInsert( [ $newTable[ 'unique_column' ] => $data[ $newTable[ 'unique_column' ] ] ], $data );
                        $effected++;
                        echo $effected . ' items inserted successfully on table ' . $newTable[ 'table' ] . " \r";
                    } catch ( \Exception $e ) {
                        $this->error( 'Error: ' . $e->getMessage() );
                    }
                }
            } );

            $this->info(  $effected . ' items inserted successfully on table ' . $newTable[ 'table' ] );
        }

        /* Sync vehicle status */
        $vehicleStatuses = DB::connection( 'amaya_db' )
            ->table('vehicle')
            ->select(DB::raw("DISTINCT status"))
            ->pluck('status')
            ->toArray();
        foreach( $vehicleStatuses as $status ) {
            $ids = DB::connection( 'amaya_db' )
                ->table('vehicle')
                ->where('status', $status)
                ->pluck('id')
                ->toArray();

            DB::table( 'vehicles')->whereIn('id', $ids)->update(['status' => $status]);
        }

        return 0;
    }
}
