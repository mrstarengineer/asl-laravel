<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up ()
    {
        Schema::table( 'customers', function ( Blueprint $table ) {
            if ( Schema::hasColumn( 'customers', 'country' ) ) {
                $table->dropColumn( 'country' );
            }

            if ( Schema::hasColumn( 'customers', 'state' ) ) {
                $table->dropColumn( 'state' );
            }

            if ( Schema::hasColumn( 'customers', 'city' ) ) {
                $table->dropColumn( 'city' );
            }

            $table->bigInteger( 'country_id' )->nullable();
            $table->bigInteger( 'state_id' )->nullable();
            $table->bigInteger( 'city_id' )->nullable();

        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down ()
    {
        //
    }
}
