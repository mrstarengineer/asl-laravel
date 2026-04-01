<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exports', function (Blueprint $table) {
            $table->id();
            $table->date('export_date')->index()->nullable();
            $table->date('loading_date')->index()->nullable();
            $table->string('broker_name', 150)->nullable();
            $table->string('booking_number', 45)->index()->nullable();
            $table->date('eta')->index()->nullable();
            $table->string('ar_number', 45)->index()->nullable();
            $table->string('xtn_number', 45)->index()->nullable();
            $table->string('seal_number', 45)->index()->nullable();
            $table->string('container_number', 45)->index()->nullable();
            $table->date('cutt_off')->nullable();
            $table->string('vessel',45)->index()->nullable();
            $table->string('voyage', 45)->nullable();
            $table->string('terminal', 45)->index()->nullable();
            $table->string('streamship_line', 150)->nullable();
            $table->string('destination', 45)->index()->nullable();
            $table->string('itn', 45)->index()->nullable();
            $table->text('contact_details')->nullable();
            $table->text('special_instruction')->nullable();
            $table->string('container_type', 45)->index()->nullable();
            $table->string('port_of_loading', 150)->index()->nullable();
            $table->string('port_of_discharge', 150)->index()->nullable();
            $table->string('export_invoice', 130)->nullable();
            $table->string('bol_note')->nullable();
            $table->bigInteger('customer_user_id')->index()->nullable();
            $table->text('bol_remarks')->nullable();
            $table->text('note')->nullable();
            $table->date('handed_over_date')->nullable();
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
        Schema::dropIfExists('exports');
    }
}
