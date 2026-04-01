<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string( 'name' );
            $table->string( 'email', 100 )->unique();
            $table->string( 'password' );
            $table->tinyInteger( 'status' )->nullable()->default( \App\Enums\VisibilityStatus::ACTIVE );
            $table->timestamp( 'email_verified_at' )->nullable();
            $table->string( 'auth_key' )->nullable();
            $table->string( 'device_id_token' )->nullable();
            $table->rememberToken();
            $table->unsignedInteger( 'role_id' )->nullable()->default( \App\Enums\Roles::CUSTOMER );
            $table->bigInteger( 'created_by' )->nullable();
            $table->bigInteger( 'updated_by' )->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('users');
    }
}
