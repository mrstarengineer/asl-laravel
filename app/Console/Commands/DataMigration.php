<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class DataMigration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:migrate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate data from old to new database';

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
        $config = config('data_migration');
        foreach ($config['table_mapping'] as $oldTale => $newTable) {
            if ( empty( $config[ 'field_mapping' ][ $newTable[ 'table' ] ] ) ) {
                $this->error( 'Field mapping not found for table: ' . $newTable[ 'table' ] );
                continue;
            }

            $orderByColumn = Schema::Connection( 'amaya_db' )->getColumnListing( $oldTale )[ 0 ];
            $effected = 0;
            DB::connection( 'amaya_db' )->table( $oldTale )->orderBy( $orderByColumn )->chunk( 5, function ( $items ) use ( $config, $newTable, &$effected, $oldTale ) {
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
                    } catch (\Exception $e) {
                        $this->error('Something went wrong');
                    }
                }
            });
            $this->info($effected.' items inserted successfully on table '.$newTable['table']);
        }
        return 0;
    }
}
