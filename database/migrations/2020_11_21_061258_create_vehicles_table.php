<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVehiclesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('hat_number', 45)->nullable();
            $table->string('vehicle_type', 100)->nullable();
            $table->string('year', 45)->index()->nullable();
            $table->string('color', 45)->nullable();
            $table->string('model', 45)->index()->nullable();
            $table->string('make', 45)->index()->nullable();
            $table->string('vin', 45)->index();
            $table->string('weight', 45)->nullable();
            $table->string('pieces', 45)->nullable();
            $table->string('value', 45)->index()->nullable();
            $table->string('license_number', 45)->index()->nullable();
            $table->string('towed_from', 45)->nullable();
            $table->string('lot_number', 45)->index();
            $table->double('towed_amount')->nullable();
            $table->double('storage_amount')->nullable();
            $table->integer('status')->index()->nullable();
            $table->string('load_status', 50)->nullable();
            $table->integer('check_number')->nullable();
            $table->double('additional_charges')->nullable();
            $table->bigInteger('location_id')->index()->nullable();
            $table->bigInteger('customer_user_id');
            $table->bigInteger('shipper_id')->nullable();
            $table->bigInteger('towing_request_id')->index()->nullable();
            $table->tinyInteger('is_export')->nullable()->default(0);
            $table->string('title_amount', 45)->nullable();
            $table->string('container_number', 50)->index()->nullable();
            $table->boolean('keys')->nullable();
            $table->string('key_note')->nullable();
            $table->string('prepared_by')->nullable();
            $table->string('auction_at', 64)->nullable();
            $table->integer('vcr')->nullable();
            $table->text('note')->nullable();
            $table->bigInteger('export_id')->index()->nullable();
            $table->bigInteger('created_by')->nullable();
            $table->bigInteger('updated_by')->nullable();
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
        Schema::dropIfExists('vehicles');
    }
}
