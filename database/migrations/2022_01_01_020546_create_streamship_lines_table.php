<?php

use App\Enums\VisibilityStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStreamshipLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up ()
    {
        Schema::create( 'streamship_lines', function ( Blueprint $table ) {
            $table->id();
            $table->string( 'name', 100 );
            $table->unsignedTinyInteger( 'status' )->nullable()->default( VisibilityStatus::ACTIVE );
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
        Schema::dropIfExists( 'streamship_lines' );
    }
}
