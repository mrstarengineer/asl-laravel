<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Brings a database built from the migrations in line with production.
 *
 * Production got these columns, indexes and defaults by hand, so the migrations never knew about them.
 * Every step checks first and does nothing where the change is already there, which makes this
 * a no-op on production and on a database imported from a production dump.
 */
class AlignSchemaWithProduction extends Migration
{
    /** table => [ index name => column ] */
    private $indexes = [
        'activity_logs'                => [ 'title_audit_log' => 'title' ],
        'customer_documents'           => [ 'idx_customer_documents_customer_user_id' => 'customer_user_id' ],
        'houstan_custom_cover_letters' => [ 'export_id' => 'export_id' ],
        'invoices'                     => [ 'idx_invoices_customer_user_id' => 'customer_user_id', 'idx_invoices_export_id' => 'export_id' ],
        'locations'                    => [ 'status' => 'status' ],
        'vehicles'                     => [ 'customer_user_id' => 'customer_user_id' ],
        'vehicle_conditions'           => [ 'idx_vehicle_id' => 'vehicle_id' ],
        'vehicle_features'             => [ 'idx_vehicle_id' => 'vehicle_id' ],
    ];

    public function up()
    {
        if ( !Schema::hasColumn( 'exports', 'shipper_id' ) ) {
            Schema::table( 'exports', function ( Blueprint $table ) {
                $table->bigInteger( 'shipper_id' )->nullable();
            } );
        }

        if ( !Schema::hasColumn( 'vehicles', 'export_order' ) ) {
            Schema::table( 'vehicles', function ( Blueprint $table ) {
                $table->smallInteger( 'export_order' )->nullable()->default( 0 );
            } );
        }

        foreach ( $this->indexes as $table => $indexes ) {
            foreach ( $indexes as $name => $column ) {
                if ( !$this->hasIndexOn( $table, $column ) ) {
                    Schema::table( $table, function ( Blueprint $blueprint ) use ( $name, $column ) {
                        $blueprint->index( $column, $name );
                    } );
                }
            }
        }

        foreach ( [ 'cust_view', 'admin_view' ] as $column ) {
            if ( $this->column( 'notes', $column )->COLUMN_DEFAULT === null ) {
                DB::statement( "ALTER TABLE `notes` ALTER COLUMN `$column` SET DEFAULT 1" );
            }
        }

        if ( $this->column( 'notifications', 'message' )->DATA_TYPE === 'text' ) {
            DB::statement( 'ALTER TABLE `notifications` MODIFY `message` MEDIUMTEXT NOT NULL' );
        }
    }

    /**
     * Not reversible: there is no way to tell which of these were already in place before up() ran.
     */
    public function down()
    {
        //
    }

    /** True when any index starts with this column, whatever the index is called. */
    private function hasIndexOn( string $table, string $column ): bool
    {
        return DB::table( 'information_schema.statistics' )
            ->where( 'table_schema', DB::getDatabaseName() )
            ->where( 'table_name', $table )
            ->where( 'column_name', $column )
            ->where( 'seq_in_index', 1 )
            ->exists();
    }

    private function column( string $table, string $column )
    {
        return DB::table( 'information_schema.columns' )
            ->where( 'table_schema', DB::getDatabaseName() )
            ->where( 'table_name', $table )
            ->where( 'column_name', $column )
            ->first( [ 'COLUMN_DEFAULT', 'DATA_TYPE' ] );
    }
}
