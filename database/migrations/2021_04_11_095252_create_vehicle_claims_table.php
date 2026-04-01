<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVehicleClaimsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up ()
    {
        Schema::create( 'vehicle_claims', function ( Blueprint $table ) {
            $table->id();
            $table->bigInteger( 'vehicle_id' );
            $table->bigInteger( 'export_id' )->nullable();
            $table->bigInteger( 'customer_user_id' );
            $table->bigInteger( 'approved_by' )->nullable();
            $table->text( 'remarks' )->nullable();
            $table->double( 'claim_amount' )->nullable();
            $table->double( 'approved_amount' )->nullable();
            $table->date( 'approved_date' )->nullable();
            $table->date( 'create_date' )->nullable();
            $table->integer( 'claim_status', 2 )->nullable()->default(10);
            $table->text( 'admin_remarks' )->nullable();
            $table->string( 'vehicle_part' )->nullable();
            $table->string( 'other_parts' )->nullable();
            $table->tinyInteger( 'cust_view' )->nullable();
            $table->tinyInteger( 'admin_view' )->nullable();
            $table->string( 'lot_number' )->nullable();
            $table->timestamps();
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down ()
    {
        Schema::dropIfExists( 'vehicle_claims' );
    }
}
