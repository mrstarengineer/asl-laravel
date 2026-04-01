<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyConsigneesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up ()
    {
        Schema::table( 'consignees', function ( Blueprint $table ) {
            if ( Schema::hasColumn( 'consignees', 'country' ) ) {
                $table->dropColumn( 'country' );
            }

            if ( Schema::hasColumn( 'consignees', 'state' ) ) {
                $table->dropColumn( 'state' );
            }

            if ( Schema::hasColumn( 'consignees', 'city' ) ) {
                $table->dropColumn( 'city' );
            }

            $table->bigInteger( 'city_id' )->nullable()->after( 'consignee_address_2' );
            $table->bigInteger( 'state_id' )->nullable()->after( 'consignee_address_2' );
            $table->bigInteger( 'country_id' )->nullable()->after( 'consignee_address_2' );

        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down ()
    {
        Schema::table( 'consignees', function ( Blueprint $table ) {
            $table->dropColumn( 'city_id' );
            $table->dropColumn( 'state_id' );
            $table->dropColumn( 'country_id' );
        } );
    }
}
