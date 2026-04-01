<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddClaimTypeToVehicleClaimsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vehicle_claims', function (Blueprint $table) {
            $table->text('misc')->nullable()->after('lot_number');
            $table->unsignedSmallInteger('type')->nullable()->default(\App\Enums\ClaimType::DAMAGE_CLAIM)->after('lot_number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vehicle_claims', function (Blueprint $table) {
            $table->dropColumn('misc');
            $table->dropColumn('type');
        });
    }
}
