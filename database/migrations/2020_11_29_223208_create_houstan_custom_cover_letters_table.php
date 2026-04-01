<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHoustanCustomCoverLettersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('houstan_custom_cover_letters', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('export_id');
            $table->string('vehicle_location', 45)->nullable();
            $table->string('exporter_id', 45)->nullable();
            $table->string('exporter_type_issuer', 45)->nullable();
            $table->string('transportation_value', 45)->nullable();
            $table->string('exporter_dob', 45)->nullable();
            $table->string('ultimate_consignee_dob', 45)->nullable();
            $table->string('consignee')->nullable();
            $table->string('notify_party')->nullable();
            $table->string('menifest_consignee')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('houstan_custom_cover_letters');
    }
}
