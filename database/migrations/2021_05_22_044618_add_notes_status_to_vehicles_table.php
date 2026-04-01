<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNotesStatusToVehiclesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up ()
    {
        Schema::table( 'vehicles', function ( Blueprint $table ) {
            $table->tinyInteger( 'notes_status' )->nullable()->default( 0 )->after( 'key_note' );
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
            $table->dropColumn( 'notes_status' );
        } );
    }
}
