<?php

namespace App\Http\Controllers\Api\V1\Reports;

use App\Enums\ContainerType;
use App\Enums\Roles;
use App\Enums\VehicleStatus;
use App\Enums\VisibilityStatus;
use App\Exports\ContainerReportExport;
use App\Exports\CustomerManagementReportExport;
use App\Exports\CustomerTitleStatusReportExport;
use App\Exports\InventoryReportExport;
use App\Exports\VehicleReportExport;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Export;
use App\Models\Vehicle;
use App\Presenters\PaginatorPresenter;
use App\Presenters\Reports\CustomerManagementPresenter;
use App\Presenters\Reports\CustomerTitleStatusPresenter;
use App\Presenters\VehiclePresenter;
use App\Services\Location\LocationService;
use App\Services\Reports\ReportService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use PDF;

class ReportController extends Controller
{
    private $service;

    /**
     * ReportController constructor.
     *
     * @param ReportService $service
     */
    public function __construct ( ReportService $service )
    {
        $this->service = $service;
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function customerManagementReport ( Request $request ): \Illuminate\Http\JsonResponse
    {
        $data = $this->service->customerManagementReport( $request->all() );
        foreach ( $data as $key => $item ) {
            $manifest_cars = $this->vehicleCountByContainerType($item->user_id);
            $shipped_cars = $this->vehicleCountByContainerType($item->user_id, VehicleStatus::SHIPPED);
            $data[$key]->manifest_cars = $manifest_cars;
            $data[$key]->shipped_cars = $shipped_cars;
            $data[$key]->total_cars =  $this->vehicleCountByCustomer($item->user_id);
            $data[$key]->total_current_cars = $item->on_hand + $item->on_the_way + $shipped_cars + $manifest_cars;
        }
        $data = ( new PaginatorPresenter( $data->toArray() ) )->presentBy( CustomerManagementPresenter::class );

        return response()->json( $data );
    }

    /**
     * @param $customerUserId
     * @param int $containerType
     * @param int $status
     */
    private function vehicleCountByContainerType ( $customerUserId, $status = VehicleStatus::MANIFEST )
    {
        return Vehicle::query()
            ->whereHas( 'export' )
            ->where( 'status', $status )
            ->where('customer_user_id', $customerUserId)
            ->count();
    }

    private function vehicleCountByCustomer ( $customerUserId )
    {
        return Vehicle::query()
            ->where('customer_user_id', $customerUserId)
            ->count();
    }

    /**
     * @param $customerUserId
     * @param int $containerType
     * @param int $status
     */
    private function containerCountByContainerType ( $customerUserId, $containerType = ContainerType::FULL_CONTAINER, $status = VehicleStatus::MANIFEST )
    {
        return Export::query()->where( 'status', $status )
            ->where( 'is_full_container', $containerType )
            ->where( 'customer_user_id', $customerUserId )
            ->count();
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function customerManagementReportExport ( Request $request ): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        if($request->auth_user_id) {
            force_login($request->auth_user_id);
        }

        return Excel::download( new CustomerManagementReportExport( $request->all() ), 'customer_management_report.xlsx' );
    }

    public function customerTitleStatusReport ( Request $request ): \Illuminate\Http\JsonResponse
    {
        $data = $this->service->customerTitleStatusReport( $request->all() )->toArray();
        $data = ( new PaginatorPresenter( $data ) )->presentBy( CustomerTitleStatusPresenter::class );

        return response()->json( $data );
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function customerTitleStatusReportExport ( Request $request ): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        if($request->auth_user_id) {
            force_login($request->auth_user_id);
        }
        return Excel::download( new CustomerTitleStatusReportExport( $request->all() ), 'customer_title_status_report.xlsx' );
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function customerInvoiceReport ( Request $request ): \Illuminate\Http\JsonResponse
    {
        $rules = [
            'customer_user_id' => 'required',
            'date_from'        => 'required',
            'date_to'          => 'required',
        ];

        $validator = Validator::make( $request->all(), $rules );
        if ( $validator->fails() ) {
            return api()->fails( $validator->messages(), Response::HTTP_UNPROCESSABLE_ENTITY );
        }

        $data = $this->service->customerInvoiceReport( $request->all() );

        return response()->json( $data );
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function customerRecordReport ( Request $request ): \Illuminate\Http\JsonResponse
    {
        $filters = $request->all();
        if ( optional(auth()->user())->role == Roles::CUSTOMER ) {
            $filters['customer_user_id'] = auth()->user()->id;
        }
        $data = $this->service->customerInvoiceReport( $filters );

        return response()->json( $data );
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function customerReport ( Request $request ): \Illuminate\Http\JsonResponse
    {
        $data = $this->service->customerReport( $request->all() );

        return response()->json( $data );
    }

    public function containerReport ( Request $request ): \Illuminate\Http\JsonResponse
    {
        $startDate = empty($request->get( 'start_date' )) ? date( 'Y-m-d' ) : $request->get( 'start_date' );
        $endDate = empty($request->get( 'end_date' )) ? date( 'Y-m-d' ) : $request->get( 'end_date' );
        $reportType = empty($request->get( 'report_type' )) ? 'container_shipped' : $request->get( 'report_type' );
        $customerUserId = $request->get( 'customer_user_id' );
        $locations = app( LocationService::class )->all( [ 'status' => VisibilityStatus::ACTIVE, 'limit' => -1 ] );
        $locationReportData = [];
        if ( optional(auth()->user())->role == Roles::CUSTOMER ) {
            $customerUserId = auth()->user()->id;
        }

        $key = 0;
        foreach ( [ VehicleStatus::MANIFEST => 'MANIFEST', VehicleStatus::ARRIVED => 'ARRIVED', VehicleStatus::SHIPPED => 'SHIPPED' ] as $status => $label ) {
            $locationReportData[ $key ][ 'title' ] = $label;
            foreach ( $locations as $location ) {
                $locationReportData[ $key ][ 'locations' ][] = [
                    'title' => $location->name,
                    'count' => $this->service->getVehicleCount( $location->id, $status, $startDate, $endDate, $customerUserId, 'container', $reportType ),
                ];
            }
            $key++;
        }

        $locationReportData[ $key ][ 'title' ] = 'DAILY ARRIVAL';
        foreach ( $locations as $location ) {
            $locationReportData[ $key ][ 'locations' ][] = [
                'title' => $location->name,
                'count' => $this->service->getVehicleCount( $location->id, VehicleStatus::ARRIVED, $startDate, $endDate, $customerUserId, 'container', $reportType ),
            ];
        }

        $data[ 'containers' ] = $this->service->containerReport( [
            'start_date'       => $startDate,
            'end_date'         => $endDate,
            'report_type'      => $reportType,
            'customer_user_id' => $customerUserId,
        ] );
        $data[ 'location_reports' ] = $locationReportData;

        return response()->json( $data );
    }

    public function containerReportExport ( Request $request )
    {
        if($request->auth_user_id) {
            force_login($request->auth_user_id);
        }
        $startDate = empty($request->get( 'start_date' )) ? date( 'Y-m-d' ) : $request->get( 'start_date' );
        $endDate = empty($request->get( 'end_date' )) ? date( 'Y-m-d' ) : $request->get( 'end_date' );
        $reportType = empty($request->get( 'report_type' )) ? 'container_shipped' : $request->get( 'report_type' );
        $customerUserId = $request->get( 'customer_user_id' );
        if ( optional(auth()->user())->role == Roles::CUSTOMER ) {
            $customerUserId = auth()->user()->id;
        }

        return Excel::download( new ContainerReportExport( [
            'start_date'       => $startDate,
            'end_date'         => $endDate,
            'report_type'      => $reportType,
            'customer_user_id' => $customerUserId,
        ] ), 'container_report.xlsx' );
    }

    public function vehicleReport ( Request $request ): \Illuminate\Http\JsonResponse
    {
        $startDate = empty($request->get( 'start_date' )) ? date( 'Y-m-d' ) : $request->get( 'start_date' );
        $endDate = empty($request->get( 'end_date' )) ? date( 'Y-m-d' ) : $request->get( 'end_date' );
        $reportType = empty($request->get( 'report_type' )) ? 'vehicle_added' : $request->get( 'report_type' );
        $customerUserId = $request->get( 'customer_user_id' );
        if ( optional(auth()->user())->role == Roles::CUSTOMER ) {
            $customerUserId = auth()->user()->id;
        }
        $locations = app( LocationService::class )->all( [ 'status' => VisibilityStatus::ACTIVE, 'limit' => -1 ] );
        $locationReportData = [];

        $key = 0;
        foreach ( [ VehicleStatus::ON_HAND => 'ON HAND', VehicleStatus::MANIFEST => 'MANIFEST', VehicleStatus::SHIPPED => 'SHIPPED', VehicleStatus::ARRIVED => 'ARRIVED' ] as $status => $label ) {
            $locationReportData[ $key ][ 'title' ] = $label;
            foreach ( $locations as $location ) {
                $locationReportData[ $key ][ 'locations' ][] = [
                    'title' => $location->name,
                    'count' => $this->service->getVehicleCount( $location->id, $status, $startDate, $endDate, $customerUserId, 'vehicle', $reportType ),
                ];
            }
            $key++;
        }

        $vehicles = $this->service->vehicleReport( [
            'start_date'       => $startDate,
            'end_date'         => $endDate,
            'report_type'      => $reportType,
            'customer_user_id' => $customerUserId,
        ] );
        $data[ 'vehicles' ] = ( new PaginatorPresenter( $vehicles->toArray() ) )->presentBy( VehiclePresenter::class );
        $data[ 'location_reports' ] = $locationReportData;

        return response()->json( $data );
    }

    public function vehicleReportExport ( Request $request ): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        if($request->auth_user_id) {
            force_login($request->auth_user_id);
        }
        $startDate = empty($request->get( 'start_date' )) ? date( 'Y-m-d' ) : $request->get( 'start_date' );
        $endDate = empty( $request->get( 'end_date' ) ) ? date( 'Y-m-d' ) : $request->get( 'end_date' );
        $reportType = empty( $request->get( 'report_type' ) ) ? 'vehicle_added' : $request->get( 'report_type' );
        $customerUserId = $request->get( 'customer_user_id' );
        if ( optional(auth()->user())->role == Roles::CUSTOMER ) {
            $customerUserId = auth()->user()->id;
        }

        return Excel::download( new VehicleReportExport( [
            'start_date'       => $startDate,
            'end_date'         => $endDate,
            'report_type'      => $reportType,
            'customer_user_id' => $customerUserId,
        ] ), 'vehicle_report.xlsx' );
    }

    public function inventoryReport ( Request $request )
    {
        return response()->json( $this->prepareInventoryData( $request ) );
    }

    public function inventoryReportExport ( Request $request )
    {
        ini_set( 'memory_limit', '7000M' );
        set_time_limit( 0 );
        if($request->auth_user_id) {
            force_login($request->auth_user_id);
        }
        return Excel::download( new InventoryReportExport( $request->all() ), 'inventory_report.xlsx' );
    }

    public function inventoryReportPdf ( Request $request )
    {
        ini_set( 'memory_limit', '7000M' );
        set_time_limit( 0 );
        if($request->auth_user_id) {
            force_login($request->auth_user_id);
        }
        $data = $this->prepareInventoryData( $request );
        $pdf = PDF::loadView( 'pdf.inventory_report', ['data' => $data] );

        return $pdf->stream( 'inventory_report.pdf' );
    }

    private function prepareInventoryData ( $request )
    {
        $results = [];
        $sort_type = $request->get( 'status' ) ? trans( 'vehicle.statuses.' . $request->status ) : 'ALL';
        $locationName = 'ALL';
        if ( $request->get( 'location' ) ) {
            $locationName = data_get( app( LocationService::class )->getById( $request->get( 'location' ) ), 'name' );
        }
        $filters = $request->all();
        if ( optional( auth()->user() )->role == Roles::CUSTOMER ) {
            $filters['customer_user_id'] = auth()->user()->id;
        }
//        if ( empty( Arr::get( $filters, 'status', '' ) ) ) {
//            $filters[ 'status' ] = [ VehicleStatus::ON_HAND, VehicleStatus::ON_THE_WAY ];
//        }
        $vehicles = $this->service->inventoryReport( $filters )->groupBy( 'customer_user_id' );

        $customers = Customer::whereIn('user_id', $vehicles->keys()->toArray())->orderBy('customer_name', 'asc')->get();

        foreach ($customers  as $customer) {
            $customerVehicles = $vehicles[$customer->user_id];
            $items = [
                'heading' => [
                    'inventory' => data_get( $customerVehicles, '0.customer.customer_name' ),
                    'company_name' => data_get( $customerVehicles, '0.customer.company_name' ),
                    'customer_id' => data_get( $customerVehicles, '0.customer.legacy_customer_id' ),
                    'sort_type' => $sort_type,
                    'location'  => $locationName,
                ],
            ];
            foreach ( $customerVehicles->sortByDesc('age') as $row ) {
                $items[ 'vehicles' ][] = [
                    'hat_no'        => $row->hat_number,
                    'date_received' => $row->status == VehicleStatus::ON_THE_WAY ? $row->towing_request->towing_request_date : $row->towing_request->deliver_date,
                    'year'          => $row->year,
                    'make'          => $row->make,
                    'model'         => $row->model,
                    'color'         => $row->color,
                    'vin'           => $row->vin,
                    'lot_number'    => $row->lot_number,
                    'title'         => $row->towing_request->title_received ? 'Yes' : 'No',
                    'title_type'    => trans( 'vehicle.title_type.' . data_get( $row, 'towing_request.title_type', 0 ) ),
                    'keys'          => $row->keys ? 'Yes' : 'No',
                    'age'           => $row->age,
                    'status'        => trans( 'vehicle.statuses.' . $row->status ),
                    'price'         => $row->value,
                    'note'          => $row->note,
                    'location'      => data_get($row, 'location.name'),
                    'yard'          => data_get($row, 'yard.name'),
                    'hybrid'        => $this->hybridName(data_get( $row, 'hybrid' ) ),
                ];
            }
            $results[] = $items;
        }

        return $results;
    }

    public function hybridName($hybrid)
    {
        if($hybrid == 1) {
           return 'Yes';
        }else if($hybrid == 2) {
            return 'No';
        }
        return '';
    }

}
