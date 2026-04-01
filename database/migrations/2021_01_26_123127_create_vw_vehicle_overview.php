<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateVwVehicleOverview extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("CREATE OR REPLACE VIEW vw_vehicle_overview AS SELECT v.id, v.customer_user_id, v.location_id, COUNT(vi.id) AS total_images
            FROM vehicles v
                     LEFT JOIN vehicle_images vi ON v.id = vi.vehicle_id
            GROUP BY v.id, v.customer_user_id, v.location_id;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('DROP VIEW vw_vehicle_overview');
    }
}
