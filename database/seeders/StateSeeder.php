<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run ()
    {
        DB::table( 'states' )->insert( [
            [ 'id' => 1, 'country_id' => 231, 'name' => 'ANCHORAGE', 'slug' => 'anchorage', 'short_code' => 'AK', 'status' => 1, ],
            [ 'id' => 2, 'country_id' => 231, 'name' => 'ALBAMA', 'slug' => 'albama', 'short_code' => 'AL', 'status' => 1, ],
            [ 'id' => 3, 'country_id' => 231, 'name' => 'ARKANSAS', 'slug' => 'arkansas', 'short_code' => 'AR', 'status' => 1, ],
            [ 'id' => 4, 'country_id' => 231, 'name' => 'ARIZONA', 'slug' => 'arizona', 'short_code' => 'AZ', 'status' => 1, ],
            [ 'id' => 5, 'country_id' => 231, 'name' => 'CALIFORNIA', 'slug' => 'california', 'short_code' => 'CA', 'status' => 1, ],
            [ 'id' => 6, 'country_id' => 231, 'name' => 'COLORADO', 'slug' => 'colorado', 'short_code' => 'CO', 'status' => 1, ],
            [ 'id' => 7, 'country_id' => 231, 'name' => 'CONNECTICUT', 'slug' => 'connecticut', 'short_code' => 'CT', 'status' => 1, ],
            [ 'id' => 8, 'country_id' => 231, 'name' => 'DELAWARE', 'slug' => 'delaware', 'short_code' => 'DE', 'status' => 1, ],
            [ 'id' => 9, 'country_id' => 231, 'name' => 'FLORIDA', 'slug' => 'florida', 'short_code' => 'FL', 'status' => 1, ],
            [ 'id' => 10, 'country_id' => 231, 'name' => 'GEORGIA', 'slug' => 'georgia', 'short_code' => 'GA', 'status' => 1, ],
            [ 'id' => 11, 'country_id' => 231, 'name' => 'HAWAII', 'slug' => 'hawaii', 'short_code' => 'HI', 'status' => 1, ],
            [ 'id' => 12, 'country_id' => 231, 'name' => 'IOWA', 'slug' => 'iowa', 'short_code' => 'IA', 'status' => 1, ],
            [ 'id' => 13, 'country_id' => 231, 'name' => 'IDAHO', 'slug' => 'idaho', 'short_code' => 'ID', 'status' => 1, ],
            [ 'id' => 14, 'country_id' => 231, 'name' => 'ILLINIOS', 'slug' => 'illinios', 'short_code' => 'IL', 'status' => 1, ],
            [ 'id' => 15, 'country_id' => 231, 'name' => 'INDIANA', 'slug' => 'indiana', 'short_code' => 'IN', 'status' => 1, ],
            [ 'id' => 16, 'country_id' => 231, 'name' => 'KANSAS', 'slug' => 'kansas', 'short_code' => 'KS', 'status' => 1, ],
            [ 'id' => 17, 'country_id' => 231, 'name' => 'KENTUCKY', 'slug' => 'kentucky', 'short_code' => 'KY', 'status' => 1, ],
            [ 'id' => 18, 'country_id' => 231, 'name' => 'LOUISISANA', 'slug' => 'louisisana', 'short_code' => 'LA', 'status' => 1, ],
            [ 'id' => 19, 'country_id' => 231, 'name' => 'MASSACHUSETTS', 'slug' => 'massachusetts', 'short_code' => 'MA', 'status' => 1, ],
            [ 'id' => 20, 'country_id' => 231, 'name' => 'MARYLAND', 'slug' => 'maryland', 'short_code' => 'MD', 'status' => 1, ],
            [ 'id' => 21, 'country_id' => 231, 'name' => 'MAINE', 'slug' => 'maine', 'short_code' => 'ME', 'status' => 1, ],
            [ 'id' => 22, 'country_id' => 231, 'name' => 'MICHIGAN', 'slug' => 'michigan', 'short_code' => 'MI', 'status' => 1, ],
            [ 'id' => 23, 'country_id' => 231, 'name' => 'MINNESOTA', 'slug' => 'minnesota', 'short_code' => 'MN', 'status' => 1, ],
            [ 'id' => 24, 'country_id' => 231, 'name' => 'MISSOURI', 'slug' => 'missouri', 'short_code' => 'MO', 'status' => 1, ],
            [ 'id' => 25, 'country_id' => 231, 'name' => 'MISSISSIPI', 'slug' => 'mississipi', 'short_code' => 'MS', 'status' => 1, ],
            [ 'id' => 26, 'country_id' => 231, 'name' => 'MONTANA', 'slug' => 'montana', 'short_code' => 'MT', 'status' => 1, ],
            [ 'id' => 27, 'country_id' => 231, 'name' => 'NORTH CAROLINA', 'slug' => 'north-carolina', 'short_code' => 'NC', 'status' => 1, ],
            [ 'id' => 28, 'country_id' => 231, 'name' => 'NORTH DAKOTA', 'slug' => 'north-dakota', 'short_code' => 'ND', 'status' => 1, ],
            [ 'id' => 29, 'country_id' => 231, 'name' => 'NEBRASKA', 'slug' => 'nebraska', 'short_code' => 'NE', 'status' => 1, ],
            [ 'id' => 30, 'country_id' => 231, 'name' => 'NEW JERSEY', 'slug' => 'new-jersey', 'short_code' => 'NJ', 'status' => 1, ],
            [ 'id' => 31, 'country_id' => 231, 'name' => 'NEW HAMPSHIRE', 'slug' => 'new-hampshire', 'short_code' => 'NH', 'status' => 1, ],
            [ 'id' => 32, 'country_id' => 231, 'name' => 'NEW MEXICO', 'slug' => 'new-mexico', 'short_code' => 'NM', 'status' => 1, ],
            [ 'id' => 33, 'country_id' => 231, 'name' => 'NEVADA', 'slug' => 'nevada', 'short_code' => 'NV', 'status' => 1, ],
            [ 'id' => 34, 'country_id' => 231, 'name' => 'NEW YORK', 'slug' => 'new-york', 'short_code' => 'NY', 'status' => 1, ],
            [ 'id' => 35, 'country_id' => 231, 'name' => 'OHIO', 'slug' => 'ohio', 'short_code' => 'OH', 'status' => 1, ],
            [ 'id' => 36, 'country_id' => 231, 'name' => 'OKLAHOMA', 'slug' => 'oklahoma', 'short_code' => 'OK', 'status' => 1, ],
            [ 'id' => 37, 'country_id' => 231, 'name' => 'OREGON', 'slug' => 'oregon', 'short_code' => 'OR', 'status' => 1, ],
            [ 'id' => 38, 'country_id' => 231, 'name' => 'PENNSYLVANIA', 'slug' => 'pennsylvania', 'short_code' => 'PA', 'status' => 1, ],
            [ 'id' => 39, 'country_id' => 231, 'name' => 'RHODE ISLAND', 'slug' => 'rhode-island', 'short_code' => 'RI', 'status' => 1, ],
            [ 'id' => 40, 'country_id' => 231, 'name' => 'SOUTH CAROLINA', 'slug' => 'south-carolina', 'short_code' => 'SC', 'status' => 1, ],
            [ 'id' => 41, 'country_id' => 231, 'name' => 'SOUTH DAKOTA', 'slug' => 'south-dakota', 'short_code' => 'SD', 'status' => 1, ],
            [ 'id' => 42, 'country_id' => 231, 'name' => 'TENNESSEE', 'slug' => 'tennessee', 'short_code' => 'TN', 'status' => 1, ],
            [ 'id' => 43, 'country_id' => 231, 'name' => 'TEXAS', 'slug' => 'texas', 'short_code' => 'TX', 'status' => 1, ],
            [ 'id' => 44, 'country_id' => 231, 'name' => 'UTAH', 'slug' => 'utah', 'short_code' => 'UT', 'status' => 1, ],
            [ 'id' => 45, 'country_id' => 231, 'name' => 'VIRGINIA', 'slug' => 'virginia', 'short_code' => 'VA', 'status' => 1, ],
            [ 'id' => 46, 'country_id' => 231, 'name' => 'VERMONT', 'slug' => 'vermont', 'short_code' => 'VT', 'status' => 1, ],
            [ 'id' => 47, 'country_id' => 231, 'name' => 'WASHINGTON', 'slug' => 'washington', 'short_code' => 'WA', 'status' => 1, ],
            [ 'id' => 48, 'country_id' => 231, 'name' => 'WISCONSIN', 'slug' => 'wisconsin', 'short_code' => 'WI', 'status' => 1, ],
            [ 'id' => 49, 'country_id' => 231, 'name' => 'WEST VIRGINIA', 'slug' => 'west-virginia', 'short_code' => 'WV', 'status' => 1, ],
            [ 'id' => 50, 'country_id' => 231, 'name' => 'WAYOMING', 'slug' => 'wayoming', 'short_code' => 'WY', 'status' => 1, ],
            [ 'id' => 52, 'country_id' => 229, 'name' => 'DUBAI', 'slug' => 'dubai-2', 'short_code' => 'DXB', 'status' => 1, ],
            [ 'id' => 54, 'country_id' => 38, 'name' => 'TORONTO', 'slug' => 'toronto', 'short_code' => 'ON', 'status' => 1, ],
            [ 'id' => 55, 'country_id' => 38, 'name' => 'MONTREAL', 'slug' => 'montreal-3', 'short_code' => 'QC', 'status' => 1, ],
            [ 'id' => 56, 'country_id' => 38, 'name' => 'HALIFAX', 'slug' => 'halifax', 'short_code' => 'NS', 'status' => 1, ],
            [ 'id' => 57, 'country_id' => 38, 'name' => 'EDMONTON', 'slug' => 'edmonton-2', 'short_code' => 'AB', 'status' => 1, ],
            [ 'id' => 58, 'country_id' => 38, 'name' => 'CALGARY', 'slug' => 'calgary-2', 'short_code' => 'AB', 'status' => 1, ],
            [ 'id' => 59, 'country_id' => 38, 'name' => 'SUDBURRY', 'slug' => 'sudburry', 'short_code' => 'IMPACT', 'status' => 0, ],
            [ 'id' => 60, 'country_id' => 38, 'name' => 'OTTAWA', 'slug' => 'ottawa', 'short_code' => 'IMPACT', 'status' => 0, ],
            [ 'id' => 61, 'country_id' => 38, 'name' => 'GRAND RIVERE', 'slug' => 'grand-rivere', 'short_code' => 'QC', 'status' => 0, ],
            [ 'id' => 62, 'country_id' => 38, 'name' => 'MONTREAL', 'slug' => 'montreal', 'short_code' => 'IMPACT', 'status' => 0, ],
            [ 'id' => 63, 'country_id' => 38, 'name' => 'MONTREAL', 'slug' => 'montreal-2', 'short_code' => 'COPART', 'status' => 0, ],
            [ 'id' => 64, 'country_id' => 38, 'name' => 'ENFIELD', 'slug' => 'enfield', 'short_code' => '', 'status' => 0, ],
            [ 'id' => 65, 'country_id' => 38, 'name' => 'MONCTION', 'slug' => 'monction', 'short_code' => '', 'status' => 0, ],
            [ 'id' => 66, 'country_id' => 38, 'name' => 'COW BOY', 'slug' => 'cow-boy', 'short_code' => '', 'status' => 0, ],
            [ 'id' => 67, 'country_id' => 38, 'name' => 'CALGARY', 'slug' => 'calgary', 'short_code' => 'IMPACT', 'status' => 0, ],
            [ 'id' => 68, 'country_id' => 38, 'name' => 'EDMONTON', 'slug' => 'edmonton', 'short_code' => 'IMPACT', 'status' => 0, ],
            [ 'id' => 69, 'country_id' => 224, 'name' => 'Mary', 'slug' => 'mary', 'short_code' => '', 'status' => 1, ],
            [ 'id' => 70, 'country_id' => 224, 'name' => 'Ashgabat', 'slug' => 'ashgabat', 'short_code' => '', 'status' => 1, ],
            [ 'id' => 71, 'country_id' => 224, 'name' => 'Charjov', 'slug' => 'charjov', 'short_code' => '', 'status' => 1, ],
            [ 'id' => 72, 'country_id' => 224, 'name' => 'Sarakhs', 'slug' => 'sarakhs', 'short_code' => '', 'status' => 1, ],
            [ 'id' => 73, 'country_id' => 1, 'name' => 'Islam Qalla', 'slug' => 'islam-qalla', 'short_code' => '', 'status' => 1, ],
            [ 'id' => 74, 'country_id' => 103, 'name' => 'Bandar Bas', 'slug' => 'bandar-bas', 'short_code' => '', 'status' => 1, ],
            [ 'id' => 75, 'country_id' => 103, 'name' => 'Bandar Linga', 'slug' => 'bandar-linga', 'short_code' => '', 'status' => 1, ],
            [ 'id' => 76, 'country_id' => 38, 'name' => 'VANCOUVER', 'slug' => 'vancouver', 'short_code' => 'BC', 'status' => 1, ],
        ] );
    }
}
