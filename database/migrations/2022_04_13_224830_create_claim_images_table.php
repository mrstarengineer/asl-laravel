<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClaimImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up ()
    {
        Schema::create( 'claim_images', function ( Blueprint $table ) {
            $table->id();
            $table->bigInteger( 'claim_id' );
            $table->string( 'image', 100 );
            $table->string( 'thumbnail', 100 )->nullable();
            $table->tinyInteger( 'type' )->nullable()->default( \App\Enums\ClaimPhotoType::CUSTOMER_PHOTO );
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
        Schema::dropIfExists( 'claim_images' );
    }
}
