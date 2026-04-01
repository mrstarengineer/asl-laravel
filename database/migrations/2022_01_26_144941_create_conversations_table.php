<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConversationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up ()
    {
        Schema::create( 'conversations', function ( Blueprint $table ) {
            $table->id();
            $table->bigInteger( 'sender_id' );
            $table->bigInteger( 'model_id' );
            $table->text( 'message' );
            $table->string( 'model', 50 )->nullable()->default( 'App\\Models\\Complain' );
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
        Schema::dropIfExists( 'conversations' );
    }
}
