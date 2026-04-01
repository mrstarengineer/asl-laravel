<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up ()
    {
        Schema::create( 'invoices', function ( Blueprint $table ) {
            $table->id();
            $table->bigInteger( 'export_id' );
            $table->bigInteger( 'customer_user_id' );
            $table->bigInteger( 'consignee_id' )->nullable();
            $table->double( 'total_amount' )->nullable();
            $table->double( 'paid_amount' )->nullable();
            $table->string( 'export_invoice', 50 )->nullable();
            $table->text( 'note' )->nullable();
            $table->double( 'adjustment_damaged' )->nullable()->default( 0 );
            $table->double( 'adjustment_storage' )->nullable()->default( 0 );
            $table->double( 'adjustment_discount' )->nullable()->default( 0 );
            $table->double( 'adjustment_other' )->nullable()->default( 0 );
            $table->string( 'currency', 50 )->nullable();
            $table->double( 'discount' )->nullable()->default( 0 );
            $table->double( 'before_discount' )->nullable();
            $table->string( 'upload_invoice', 100 )->nullable();
            $table->tinyInteger( 'seen_by_customer' )->nullable()->default( 0 );
            $table->string( 'clearance_invoice', 200 )->nullable();
            $table->text( 'note2' )->nullable();
            $table->bigInteger( 'created_by' )->nullable();
            $table->bigInteger( 'updated_by' )->nullable();
            $table->timestamp( 'deleted_at' )->nullable();
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
        Schema::dropIfExists( 'invoices' );
    }
}
