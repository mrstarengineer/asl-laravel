<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivityLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up ()
    {
        Schema::create( 'activity_logs', function ( Blueprint $table ) {
            $table->id();
            $table->bigInteger( 'user_id' );
            $table->bigInteger( 'model_id' )->nullable();
            $table->string( 'title' );
            $table->string( 'type', 20 )->nullable()->default( \App\Enums\ActivityType::CREATE );
            $table->mediumText( 'logs' )->nullable();
            $table->mediumText( 'request_data' )->nullable();
            $table->string( 'platform', 50 )->nullable();
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
        Schema::dropIfExists( 'activity_logs' );
    }
}
