<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusToExportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up ()
    {
        Schema::table( 'exports', function ( Blueprint $table ) {
            $table->integer( 'status' )->nullable()->after( 'id' )->index();
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
            $table->dropColumn( 'status' );
        } );
    }
}
