<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnSubjectToComplainsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up ()
    {
        Schema::table( 'complains', function ( Blueprint $table ) {
            $table->dropColumn( 'vehicle_id' );
            $table->string( 'subject' )->after( 'id' );
            $table->tinyInteger( 'read_by_admin' )->nullable()
                ->default( \App\Enums\ReadStatus::UNREAD )
                ->after( 'note' );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down ()
    {
        Schema::table( 'complains', function ( Blueprint $table ) {
            //
        } );
    }
}
