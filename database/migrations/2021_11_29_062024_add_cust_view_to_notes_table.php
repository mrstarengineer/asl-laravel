<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCustViewToNotesTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'notes', function( Blueprint $table ) {
            $table->boolean( 'cust_view' )->nullable()->after( 'image_url' );
            $table->boolean( 'admin_view' )->nullable()->after( 'image_url' );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'notes', function( Blueprint $table ) {
            $table->dropColumn( 'cust_view' );
            $table->dropColumn( 'admin_view' );
        } );
    }
}
