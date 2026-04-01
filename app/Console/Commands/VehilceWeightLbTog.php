<?php

namespace App\Console\Commands;

use App\Models\Vehicle;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class VehilceWeightLbTog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vehicle:lbtokg {limit} {offset}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate data from old to new database';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $limit = $this->argument('limit');
        $offset = $this->argument('offset');

        $vehicles = Vehicle::orderBy('id', 'desc')
            ->skip($offset)
            ->take($limit)
            ->get();

        foreach ( $vehicles as $values ) {

            $converted = $this->convertWeight($values->weight);

            if ( $converted ) {

                Vehicle::where('id', $values->id)->update([
                    'weight'       => $converted['lbs'],
                    'weight_in_kg' => $converted['kg'],
                ]);
                $this->info($values->id . ' vehicle id and vin ' . $values->vin . ' successfully updated');
            } else {
                $this->info($values->id . ' vehicle id and vin ' . $values->vin . ' not updated');
            }

        }
        return 0;
    }

    public function convertWeight( $weightString )
    {

        if ( preg_match('/(\d+)\s*(lbs?|LBS?|Lb|LB)?/i', $weightString, $matches) ) {
            $lbs = (int)$matches[1]; // Extract integer pounds
            $kg = round($lbs * 0.453592); // Convert to kg and round to nearest integer

            return [
                'lbs' => $lbs,
                'kg'  => $kg,
            ];
        }

        return null;
    }
}
