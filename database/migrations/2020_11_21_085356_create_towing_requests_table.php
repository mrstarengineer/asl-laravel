<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTowingRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('towing_requests', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('condition')->nullable();
            $table->tinyInteger('damaged')->nullable();
            $table->boolean('pictures')->nullable();
            $table->boolean('towed')->nullable();
            $table->boolean('title_received')->nullable();
            $table->date('title_received_date')->index()->nullable();
            $table->string('title_number', 50)->nullable();
            $table->string('title_state', 50)->nullable();
            $table->date('towing_request_date')->index()->nullable();
            $table->date('pickup_date')->index()->nullable();
            $table->date('deliver_date')->index()->nullable();
            $table->text('note');
            $table->tinyInteger('title_type')->index()->nullable()->default(0);
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
        Schema::dropIfExists('towing_requests');
    }
}
