<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeletedAtToExportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up ()
    {
        Schema::table( 'exports', function ( Blueprint $table ) {
            $table->timestamp( 'deleted_at' )->nullable()->after( 'updated_by' );
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
            $table->dropColumn( 'deleted_at' );
        } );
    }
}
