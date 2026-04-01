<?php


namespace App\Services\Reports;


use App\Enums\Roles;
use App\Enums\VehicleStatus;
use App\Enums\VisibilityStatus;
use App\Models\Customer;
use App\Models\Export;
use App\Models\Invoice;
use App\Models\TowingRequest;
use App\Models\Vehicle;
use App\Services\BaseService;
use App\Services\Customer\CustomerService;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ReportService extends BaseService
{
    public function customerManagementReport( array $filters = [] )
    {
        $query = Customer::with( 'invoices' )
            ->select( [
                'customers.id',
                'customers.company_name',
                'customers.user_id',
                'customers.legacy_customer_id',
                'customers.customer_name',
                DB::raw('COUNT(vehicles.id) AS total_cars'),
                DB::raw( 'SUM( IF(vehicles.status = 1, 1, 0) ) AS on_hand' ),
                DB::raw( 'SUM( IF(vehicles.status = 2, 1, 0) ) AS manifest' ),
                DB::raw( 'SUM( IF(vehicles.status = 3, 1, 0) ) AS on_the_way' ),
                DB::raw( 'SUM( IF(vehicles.status = 4, 1, 0) ) AS shipped' ),
                DB::raw( 'SUM(CASE
                    WHEN vehicles.status IN (1,2,3,4)
                    THEN vehicles.value
                    ELSE 0
                END) AS total_value_of_vehicles' ),
            ] )
            ->join( 'vehicles', 'vehicles.customer_user_id', '=', 'customers.user_id' )
            ->leftJoin( 'users', 'vehicles.customer_user_id', '=', 'users.id' )
            ->whereNull( 'vehicles.deleted_at' );

        if ( in_array( optional( auth()->user() )->role, [ Roles::LOCATION_ADMIN, Roles::LOCATION_VIEW_ADMIN ] ) ) {
            $query->whereIn( 'vehicles.location_id', auth()->user()->locations );
        }

        if ( !empty( $filters[ 'report_global_search' ] ) ) {
            $query->where( function ( $q ) use ( $filters ) {
                $q->where( 'customers.legacy_customer_id', $filters[ 'report_global_search' ] )
                    ->orWhere( DB::raw( 'LOWER(customers.customer_name)' ), 'LIKE', '%' . strtolower( $filters[ 'report_global_search' ] ) . '%' )
                    ->orWhere( DB::raw( 'LOWER(customers.company_name)' ), 'LIKE', '%' . strtolower( $filters[ 'report_global_search' ] ) . '%' )
                    ->orWhere( DB::raw( 'LOWER(users.email)' ), strtolower( $filters[ 'report_global_search' ] ) )
                    ->orWhere( 'customers.tax_id', $filters[ 'report_global_search' ] );
            } );
        }

        $query->groupBy( [ 'customers.id' ] )
            ->orderBy( 'customers.customer_name' );

        $limit = Arr::get( $filters, 'limit', 20 );

        return $limit != '-1' ? $query->paginate( $limit ) : $query->get();
    }

    public function customerTitleStatusReport( array $filters = [] )
    {
        $query = TowingRequest::query()
            ->select( [
                'customers.user_id',
                'customers.legacy_customer_id',
                'customers.customer_name',
                DB::raw( 'SUM( IF(vehicles.status = 1, 1, 0) ) AS on_hand' ),
                DB::raw( 'SUM( IF(vehicles.status = 1, 1, 0)  AND IF(towing_requests.title_type = 1, 1, 0)) AS exportable' ),
                DB::raw( 'SUM( IF(vehicles.status = 1, 1, 0)  AND IF(towing_requests.title_type = 0, 1, 0)) AS no_title' ),
                DB::raw( 'SUM( IF(vehicles.status = 3, 1, 0) ) AS on_the_way' ),
                DB::raw( 'SUM( IF(vehicles.status = 1, 1, 0) AND IF(towing_requests.title_type = 2, 1, 0) ) AS pending' ),
                DB::raw( 'SUM( IF(vehicles.status = 1, 1, 0) AND IF(towing_requests.title_type = 3, 1, 0)) AS bos' ),
                DB::raw( 'SUM( IF(vehicles.status = 1, 1, 0) AND IF(towing_requests.title_type = 4, 1, 0)) AS lien' ),
                DB::raw( 'SUM( IF(vehicles.status = 1, 1, 0) AND IF(towing_requests.title_type = 6, 1, 0)) AS rejected' ),
                DB::raw( 'SUM( IF(vehicles.status = 1, 1, 0) AND IF(towing_requests.title_type = 5, 1, 0)) AS mv907' ),
            ] )
            ->join( 'vehicles', 'vehicles.towing_request_id', '=', 'towing_requests.id' )
            ->join( 'customers', 'vehicles.customer_user_id', '=', 'customers.user_id' )
            ->leftJoin( 'users', 'vehicles.customer_user_id', '=', 'users.id' )
            ->whereNull( 'vehicles.deleted_at' );

        if ( !empty( $filters[ 'report_global_search' ] ) ) {
            $query->where( 'customers.legacy_customer_id', $filters[ 'report_global_search' ] )
                ->orWhere( DB::raw( 'LOWER(customers.customer_name)' ), 'LIKE', '%' . strtolower( $filters[ 'report_global_search' ] ) . '%' )
                ->orWhere( DB::raw( 'LOWER(customers.company_name)' ), 'LIKE', '%' . strtolower( $filters[ 'report_global_search' ] ) . '%' )
                ->orWhere( DB::raw( 'LOWER(users.email)' ), strtolower( $filters[ 'report_global_search' ] ) )
                ->orWhere( 'customers.tax_id', $filters[ 'report_global_search' ] );
        }

        if ( in_array( optional( auth()->user() )->role, [ Roles::LOCATION_ADMIN, Roles::LOCATION_VIEW_ADMIN ] ) ) {
            $query->whereIn( 'vehicles.location_id', auth()->user()->locations );
        }

        if ( optional( auth()->user() )->role == Roles::CUSTOMER ) {
            $query->where( 'customers.user_id', auth()->user()->id );
        }

        $query->groupBy( [ 'customers.user_id', 'customers.customer_name' ] )
            ->orderBy( 'customers.customer_name' );

        $limit = Arr::get( $filters, 'limit', 20 );

        return $limit != '-1' ? $query->paginate( $limit ) : $query->get();
    }

    /**
     * @param array $filters
     * @return array
     */
    public function customerInvoiceReport( array $filters = [] ): array
    {
        $invoices = $this->invoiceData( $filters );

        return [
            'invoices'      => $invoices,
            'total_paid'    => number_format( $invoices->sum( 'paid_amount' ), 2 ),
            'total_pending' => number_format( $invoices->sum( 'due_amount' ), 2 ),
            'grand_total'   => number_format( $invoices->sum( 'total_amount' ), 2 ),
        ];
    }

    /**
     * @param array $filters
     *
     * @return array
     */
    public function customerReport( array $filters = [] ): array
    {
        $query = Invoice::query()
            ->select( [
                DB::raw( 'CAST(SUM(invoices.total_amount) AS DECIMAL(10,2)) AS grand_total' ),
                //                DB::raw( 'CAST(SUM(invoices.paid_amount) AS DECIMAL(10,2)) AS total_paid' ),
                DB::raw( 'CAST(SUM(invoices.adjustment_discount) AS DECIMAL(10,2)) AS total_adjustment' ),
                DB::raw( 'CAST(SUM(invoices.total_amount) - SUM(invoices.paid_amount) AS DECIMAL(10,2)) AS total_pending' ),
            ] )
            ->join( 'customers', 'invoices.customer_user_id', '=', 'customers.user_id' )
            ->whereNull( 'customers.deleted_at' );

        if ( !empty( $filters[ 'customer_user_id' ] ) ) {
            $query->where( 'customer_user_id', $filters[ 'customer_user_id' ] );
        }

        $data = $query->first()->toArray();
        $data[ 'customers' ] = app( CustomerService::class )->all( $filters );

        return $data;
    }

    private function invoiceData( array $filters = [] )
    {
        $query = Invoice::query()
            ->select( [
                DB::raw( 'invoices.id AS invoice_id' ),
                'exports.container_number',
                'exports.booking_number',
                'vessel',
                DB::raw( 'invoices.created_at AS date' ),
                DB::raw( 'CAST(invoices.total_amount AS DECIMAL(10,2)) AS total_amount' ),
                DB::raw( 'CAST(invoices.paid_amount AS DECIMAL(10,2)) AS paid_amount' ),
                DB::raw( 'CAST(invoices.adjustment_discount AS DECIMAL(10,2)) AS adjustment_discount' ),
                DB::raw( 'CAST(invoices.total_amount - invoices.paid_amount AS DECIMAL(10,2)) AS due_amount' ),
            ] )
            ->join( 'exports', 'invoices.export_id', '=', 'exports.id' )
            ->join( 'customers', 'invoices.customer_user_id', '=', 'customers.user_id' );//->whereNull( 'customers.deleted_at' );

        if ( !empty( $filters[ 'customer_user_id' ] ) ) {
            $query->where( 'invoices.customer_user_id', $filters[ 'customer_user_id' ] );
        }

        if ( !empty( $filters[ 'date_from' ] ) && !empty ( $filters[ 'date_to' ] ) ) {
            $query->whereBetween( 'invoices.created_at', [
                Carbon::parse( $filters[ 'date_from' ] )->format( 'Y-m-d' ),
                Carbon::parse( $filters[ 'date_to' ] )->format( 'Y-m-d' ),
            ] );
        }

        return $query->get();
    }

    public function containerReport( $filters = [] )
    {
        $reportTypeMapping = [
            'container_shipped'    => 'exports.export_date',
            'container_loaded'     => 'exports.loading_date',
            'container_arrived'    => 'exports.eta',
            'manifested_container' => 'exports.created_at',
        ];

        $query = Export::query()
            ->select( [
                'exports.id',
                'exports.loading_date',
                'exports.export_date',
                'exports.eta',
                'exports.booking_number',
                'exports.container_number',
                'customers.customer_name',
                'exports.terminal',
                'exports.vessel',
            ] )
            ->join( 'vehicle_exports', 'exports.id', '=', 'vehicle_exports.export_id' )
            ->join( 'vehicles', 'vehicle_exports.vehicle_id', '=', 'vehicles.id' )
            ->join( 'towing_requests', 'towing_requests.id', '=', 'vehicles.towing_request_id' )
            ->join( 'customers', 'vehicles.customer_user_id', '=', 'customers.user_id' )
            ->join( 'locations', 'locations.id', '=', 'vehicles.location_id' )
            ->where( 'locations.status', '=', VisibilityStatus::ACTIVE );

        if ( array_key_exists( $filters[ 'report_type' ], $reportTypeMapping ) && !empty( $filters[ 'start_date' ] ) ) {
            $query->whereDate( $reportTypeMapping[ $filters[ 'report_type' ] ], '>=', $filters[ 'start_date' ] );
        } elseif ( array_key_exists( $filters[ 'report_type' ], $reportTypeMapping ) && !empty( $filters[ 'end_date' ] ) ) {
            $query->whereDate( $reportTypeMapping[ $filters[ 'report_type' ] ], '<=', $filters[ 'end_date' ] );
        }

        if ( in_array( optional( auth()->user() )->role, [ Roles::LOCATION_ADMIN, Roles::LOCATION_VIEW_ADMIN ] ) ) {
            $query->whereIn( 'vehicles.location_id', auth()->user()->locations );
        }

        $query->groupBy( [
            'exports.id',
            'exports.loading_date',
            'exports.export_date',
            'exports.eta',
            'exports.booking_number',
            'exports.container_number',
            'customers.customer_name',
            'exports.terminal',
            'exports.vessel',
        ] )
            ->orderBy( 'exports.id', 'desc' );

        $limit = Arr::get( $filters, 'limit', 20 );

        if ( $limit != -1 ) {
            return $query->paginate( $limit );
        }

        return Arr::get( $filters, 'query_only', false ) ? $query : $query->get();
    }

    /**
     * @param array $filters
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function vehicleReport( $filters = [] )
    {
        $reportTypeMapping = [
            'vehicle_added'       => 'vehicles.created_at',
            'vehicle_exported'    => 'exports.export_date',
            'vehicle_loaded'      => 'exports.loading_date',
            'vehicle_on_hand'     => 'towing_requests.deliver_date',
            'vehicle_on_the_way'  => 'towing_requests.towing_request_date',
            'vehicle_on_manifest' => 'exports.created_at',
            'vehicle_shipped'     => 'exports.export_date',
            'vehicle_arrived'     => 'exports.eta',
        ];

        $query = Vehicle::query()
            ->select( [
                'vehicles.id',
                'vehicles.year',
                'vehicles.make',
                'vehicles.model',
                'vehicles.color',
                'vehicles.vin',
                'vehicles.customer_user_id',
                'vehicles.lot_number',
                'customers.customer_name',
                'vehicles.location_id',
                'vehicles.status',
            ] )
            ->with( 'location', 'customer' )
            ->join( 'towing_requests', 'towing_requests.id', '=', 'vehicles.towing_request_id' )
            ->join( 'customers', 'customers.user_id', '=', 'vehicles.customer_user_id' )
            ->leftJoin( 'vehicle_exports', 'vehicles.id', '=', 'vehicle_exports.vehicle_id' )
            ->leftJoin( 'exports', 'vehicle_exports.export_id', '=', 'exports.id' )
            ->join( 'locations', 'locations.id', '=', 'vehicles.location_id' )
            ->where( 'locations.status', '=', VisibilityStatus::ACTIVE );

//        if ( in_array( optional( auth()->user() )->role, [ Roles::LOCATION_ADMIN, Roles::LOCATION_VIEW_ADMIN ] ) ) {
//            $query->whereIn( 'vehicles.location_id', auth()->user()->locations );
//        }

        if(auth()->user()->role == 2) {
            if( ! in_array(16, optional(auth()->user())->locations ?? [] ) ) {
                $query->where('location_id', '!=', 16);
            }
        }else if(  ! in_array(auth()->user()->role,  explode(",", env('CHINA_SHOW_ROLES') )  ) ) {
            $query->where('location_id', '!=', 16);
        }

        if ( array_key_exists( $filters[ 'report_type' ], $reportTypeMapping ) && !empty( $filters[ 'start_date' ] ) ) {
            $query->whereDate( $reportTypeMapping[ $filters[ 'report_type' ] ], '>=', $filters[ 'start_date' ] );
        } elseif ( array_key_exists( $filters[ 'report_type' ], $reportTypeMapping ) && !empty( $filters[ 'end_date' ] ) ) {
            $query->whereDate( $reportTypeMapping[ $filters[ 'report_type' ] ], '<=', $filters[ 'end_date' ] );
        }

        $query->orderBy( 'vehicles.id', 'desc' );

        $limit = Arr::get( $filters, 'limit', 20 );

        if ( $limit != -1 ) {
            return $query->paginate( $limit );
        }

        return Arr::get( $filters, 'query_only', false ) ? $query : $query->get();
    }

    public function getVehicleCount( $location, $status, $startDate, $endDate, $customerUserId, $reportType = 'container', $reportSubType = null )
    {
        $query = Vehicle::query()
            ->join( 'towing_requests', 'towing_requests.id', '=', 'vehicles.towing_request_id' )
            ->join( 'customers', 'customers.user_id', '=', 'vehicles.customer_user_id' )
            ->join( 'locations', 'locations.id', '=', 'vehicles.location_id' )
            ->leftJoin( 'vehicle_exports', 'vehicles.id', '=', 'vehicle_exports.vehicle_id' )
            ->leftJoin( 'exports', 'exports.id', '=', 'vehicle_exports.export_id' )
            ->where( 'vehicles.location_id', $location )
            ->where( 'locations.status', VisibilityStatus::ACTIVE )
            ->where( 'vehicles.status', $status );

        if ( $customerUserId ) {
            $query->where( 'vehicles.customer_user_id', $customerUserId );
        }

        if ( $reportType == 'vehicle' ) {
            $query->select( 'vehicles.id' );
            $reportTypeMapping = [
                'vehicle_added'       => 'vehicles.created_at',
                'vehicle_exported'    => 'exports.export_date',
                'vehicle_loaded'      => 'exports.loading_date',
                'vehicle_on_hand'     => 'towing_requests.deliver_date',
                'vehicle_on_the_way'  => 'towing_requests.towing_request_date',
                'vehicle_on_manifest' => 'exports.created_at',
                'vehicle_shipped'     => 'exports.export_date',
                'vehicle_arrived'     => 'exports.eta',
            ];

            $query->whereDate( $reportTypeMapping[ $reportSubType ], '>=', $startDate );
            $query->whereDate( $reportTypeMapping[ $reportSubType ], '<=', $endDate );

            $query->groupBy( 'vehicles.id' );
        } else {
            $query->select( 'exports.id' );
            $reportTypeMapping = [
                'container_shipped'    => 'exports.export_date',
                'container_loaded'     => 'exports.loading_date',
                'container_arrived'    => 'exports.eta',
                'manifested_container' => 'exports.created_at',
            ];

            $query->whereDate( $reportTypeMapping[ $reportSubType ], '>=', $startDate );
            $query->whereDate( $reportTypeMapping[ $reportSubType ], '<=', $endDate );

            $query->groupBy( 'exports.id' );
        }

        return $query->count();
    }

    public function inventoryReport( $filters = [] )
    {
        $query = Vehicle::with( [ 'towing_request', 'location', 'customer', 'export', 'yard' ] );

        if ( isset( $filters[ 'status' ] ) ) {
            $query->whereIn( 'vehicles.status', explode( ',', $filters[ 'status' ] ) );
        } else {
            $query->whereIn( 'vehicles.status', [ VehicleStatus::ON_HAND, VehicleStatus::ON_THE_WAY ] );
        }

        if ( isset( $filters[ 'customer_user_id' ] ) ) {
            $query->where( 'vehicles.customer_user_id', $filters[ 'customer_user_id' ] );
        }

        if ( in_array( optional( auth()->user() )->role, [ Roles::LOCATION_ADMIN, Roles::LOCATION_VIEW_ADMIN, Roles::LOCATION_RESTRICTED_ADMIN ] ) ) {
            $query->whereIn( 'vehicles.location_id', auth()->user()->locations );
        }

        if ( optional( auth()->user() )->customers ) {
            $query->whereHas( 'customer', function ( $q ) {
                $q->whereIn( 'legacy_customer_id', auth()->user()->customers );
            } );
        }

        if ( isset( $filters[ 'location' ] ) ) {
            $query->where( 'vehicles.location_id', $filters[ 'location' ] );
        }

        return Arr::get( $filters, 'query_only' ) ? $query : $query->get();
    }
}
