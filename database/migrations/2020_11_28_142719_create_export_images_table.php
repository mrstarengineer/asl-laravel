<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExportImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up ()
    {
        Schema::create( 'export_images', function ( Blueprint $table ) {
            $table->id();
            $table->integer( 'export_id' )->index();
            $table->string( 'name' )->nullable();
            $table->string( 'thumbnail' )->nullable();
            $table->string( 'baseurl' )->nullable();
            $table->tinyInteger( 'type' )->nullable()->default( \App\Enums\ExportPhotoType::EXPORT_PHOTO );
            $table->softDeletes();
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
        Schema::dropIfExists( 'export_images' );
    }
}
