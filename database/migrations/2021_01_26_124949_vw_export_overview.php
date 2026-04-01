<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class VwExportOverview extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("CREATE OR REPLACE VIEW vw_export_overview AS SELECT e.id, e.customer_user_id, COUNT(DISTINCT ei.id) AS total_images
            FROM exports e
                LEFT JOIN export_images ei ON e.id = ei.export_id
            GROUP BY e.id,e.customer_user_id;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('DROP VIEW vw_export_overview');
    }
}
