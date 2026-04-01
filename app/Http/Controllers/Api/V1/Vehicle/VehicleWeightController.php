<?php

namespace App\Http\Controllers\Api\V1\Vehicle;

use App\Exports\CustomersExport;
use App\Exports\VehicleWeightExport;
use App\Http\Controllers\Controller;
use App\Presenters\PaginatorPresenter;
use App\Presenters\VehicleWeightPresenter;
use App\Services\VehicleWeight\VehicleWeightService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Mockery\Exception;

class VehicleWeightController extends Controller
{
    private $service;

    public function __construct ( VehicleWeightService $service )
    {
        $this->service = $service;
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index ( Request $request ): \Illuminate\Http\JsonResponse
    {
        $data = $this->service->all( $request->all() )->toArray();

        $data = ( new PaginatorPresenter( $data ) )->presentBy( VehicleWeightPresenter::class );

        return response()->json( $data );
    }

    /**
     * @param Request $request
     */
    public function store ( Request $request )
    {
        $rules = [
            'year'   => 'required',
            'maker'  => 'required',
            'model'  => 'required',
            'weight' => 'required',
        ];
        $this->validate( $request, $rules );

        DB::beginTransaction();
        try {
            $data = $this->service->store( $request->all() );
            DB::commit();

            debug_log( "Vehicle Weight created successfully!", $data );

            return api( $data )->success( 'Vehicle Weight Created successfully!', Response::HTTP_CREATED );
        } catch ( \Exception $e ) {
            debug_log( "Vehicle Weight create failed!", $e->getTrace() );
            DB::rollback();

            return api()->fails( $e->getMessage(), Response::HTTP_BAD_REQUEST );
        }
    }

    /**
     *
     * @param $id
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show ( $id, Request $request ): \Illuminate\Http\JsonResponse
    {
        $data = $this->service->getById( $id )->toArray();
        $data = ( new VehicleWeightPresenter( $data ) )->get();

        return api( $data )->success( 'Success!' );
    }

    /**
     *
     * @param $id
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update ( $id, Request $request ): \Illuminate\Http\JsonResponse
    {
        $rules = [
            'year'   => 'required',
            'maker'  => 'required',
            'model'  => 'required',
            'weight' => 'required',
        ];
        $this->validate( $request, $rules );

        DB::beginTransaction();
        try {
            $data = $this->service->update( $id, $request->all() );
            DB::commit();

            debug_log( "Vehicle Weight updated successfully!", $data );

            return api( $data )->success( 'Vehicle Weight Updated successfully!', Response::HTTP_CREATED );
        } catch ( \Exception $e ) {
            debug_log( "Vehicle Weight update failed!", $e->getTrace() );
            DB::rollback();

            return api()->fails( $e->getMessage(), Response::HTTP_BAD_REQUEST );
        }
    }

    /**
     *
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy ( $id ): \Illuminate\Http\JsonResponse
    {
        try {
            $data = $this->service->destroy( $id );

            debug_log( "Vehicle Weight deleted successfully!", $data );

            return api( $data )->success( 'Vehicle Weight Deleted Successfully!' );
        } catch ( Exception $e ) {
            debug_log( "Vehicle Weight deletion failed!", $e->getTrace() );

            return api()->fails( $e->getMessage(), Response::HTTP_BAD_REQUEST );
        }
    }

    public function exportExcel ( Request $request )
    {
        return Excel::download( new VehicleWeightExport( $request->all() ), 'vehicle_weights.xlsx' );
    }
}
