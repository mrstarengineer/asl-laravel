<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StreamshipLineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('streamship_lines')->insert([
            [ 'name' => 'MAERSK' ],
            [ 'name' => 'MSC' ],
            [ 'name' => 'SAFMARINE' ],
            [ 'name' => 'MEDITERRANEAN' ],
            [ 'name' => 'UN' ],
            [ 'name' => 'OOCL' ],
            [ 'name' => 'ONE' ],
            [ 'name' => 'EVERGREEN' ],
            [ 'name' => 'YANG MING' ],
            [ 'name' => 'HMM' ],
            [ 'name' => 'N/A' ],
            [ 'name' => 'PIL' ],
            [ 'name' => 'APL' ],
            [ 'name' => 'WBCT' ],
            [ 'name' => 'HYUNDAI MERCHANT MARINE' ],
            [ 'name' => 'CMA CGM' ],
            [ 'name' => 'COSCO' ],
            [ 'name' => 'HAPAG LLOYD' ],
            [ 'name' => 'MC' ],
            [ 'name' => 'YM' ],
            [ 'name' => 'SEALAND' ],
            [ 'name' => 'MOL' ],
            [ 'name' => 'ITS' ],
            [ 'name' => 'MCS' ],
            [ 'name' => 'MMSC' ],
            [ 'name' => 'YML' ],
            [ 'name' => 'MSK' ],
            [ 'name' => 'MSKU' ],
            [ 'name' => 'YLM' ],
            [ 'name' => 'YMLU' ],
            [ 'name' => 'HAP' ],
            [ 'name' => 'UM' ],
            [ 'name' => 'COS' ],
            [ 'name' => 'CMA' ],
            [ 'name' => 'HLCU' ],
        ]);
    }
}
