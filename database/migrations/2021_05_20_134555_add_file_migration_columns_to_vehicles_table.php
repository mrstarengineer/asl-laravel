<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFileMigrationColumnsToVehiclesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up ()
    {
        Schema::table( 'vehicles', function ( Blueprint $table ) {
            $table->tinyInteger( 'photos_migrated' )->nullable()->default( 0 );
            $table->tinyInteger( 'documents_migrated' )->nullable()->default( 0 );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down ()
    {
        Schema::table( 'vehicles', function ( Blueprint $table ) {
            $table->dropColumn( 'photos_migrated' );
            $table->dropColumn( 'documents_migrated' );
        } );
    }
}
