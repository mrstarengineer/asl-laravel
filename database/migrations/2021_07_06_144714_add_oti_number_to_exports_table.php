<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOtiNumberToExportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up ()
    {
        Schema::table( 'exports', function ( Blueprint $table ) {
            $table->string( 'oti_number', 50 )->nullable();
            $table->tinyInteger( 'notes_status' )->nullable();
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down ()
    {
        Schema::table( 'exports', function ( Blueprint $table ) {
            $table->dropColumn( 'oti_number' );
            $table->dropColumn( 'notes_status' );
        } );
    }
}
