<?php

namespace App\Console\Commands;

use App\Models\Customer;
use App\Models\Export;
use App\Models\Invoice;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncDeletedItem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:deleted-items';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $this->syncDeletedVehicles();
        $this->syncDeletedContainers();
        $this->syncDeletedCustomers();
        $this->syncDeletedInvoices();

        return 0;
    }

    private function syncDeletedVehicles () {
        $ids = DB::connection( 'amaya_db' )->table( 'vehicle' )->where('vehicle_is_deleted', 1)->pluck('id');
        Vehicle::whereIn('id', $ids->toArray())->delete();
    }

    private function syncDeletedContainers () {
        $ids = DB::connection( 'amaya_db' )->table( 'export' )->where('export_is_deleted', 1)->pluck('id');
        Export::whereIn('id', $ids->toArray())->delete();
    }

    private function syncDeletedCustomers () {
        $ids = DB::connection( 'amaya_db' )->table( 'customer' )->where('is_deleted', 1)->pluck('user_id');
        Customer::whereIn('user_id', $ids->toArray())->delete();
        User::whereIn('id', $ids->toArray())->delete();
    }

    private function syncDeletedInvoices () {
        $ids = DB::connection( 'amaya_db' )->table( 'invoice' )->where('invoice_is_deleted', 1)->pluck('id');
        Invoice::whereIn('id', $ids->toArray())->delete();
    }
}
