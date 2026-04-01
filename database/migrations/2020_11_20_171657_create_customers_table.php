<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unique();
            $table->string('customer_name', 130)->index();
            $table->string('company_name', 130)->index();
            $table->string('phone', 20)->index()->nullable();
            $table->string('phone_two', 100)->nullable();
            $table->text('address_line_1')->nullable();
            $table->text('address_line_2')->nullable();
            $table->string('city', 45)->nullable();
            $table->string('state', 130)->nullable();
            $table->string('country', 50)->nullable();
            $table->string('zip_code', 120)->nullable();
            $table->string('tax_id', 20)->nullable();
            $table->string('fax', 50)->nullable();
            $table->string('trn', 50)->nullable();
            $table->string('other_emails', 150)->nullable();
            $table->text('note')->nullable();
            $table->bigInteger('created_by')->nullable();
            $table->bigInteger('updated_by')->nullable();
            $table->softDeletes();
            $table->bigInteger('legacy_customer_id')->nullable();
            $table->tinyInteger('loading_type')->index()->nullable();
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
        Schema::dropIfExists('customers');
    }
}
