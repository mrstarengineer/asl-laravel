<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTypeToVehicleDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up ()
    {
        Schema::table( 'vehicle_documents', function ( Blueprint $table ) {
            $table->tinyInteger( 'doc_type' )->nullable()->default( \App\Enums\VehicleDocumentType::DOCUMENT )->after( 'invoice_id' );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down ()
    {
        Schema::table( 'vehicle_documents', function ( Blueprint $table ) {
            $table->dropColumn( 'doc_type' );
        } );
    }
}
