<?php

namespace App\Http\Controllers\Api\V1\Pricing;

use App\Http\Controllers\Controller;
use App\Presenters\PaginatorPresenter;
use App\Presenters\PricingPresenter;
use App\Services\Pricing\PricingService;
use App\Services\Storage\FileManager;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Mockery\Exception;

class PricingController extends Controller
{
    private $service;

    public function __construct( PricingService $service )
    {
        $this->service = $service;
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index( Request $request ): \Illuminate\Http\JsonResponse
    {
        $data = $this->service->all( $request->all() )->toArray();
        $data = ( new PaginatorPresenter( $data ) )->presentBy( PricingPresenter::class );

        return response()->json( $data );
    }

    /**
     * @param Request $request
     */
    public function store( Request $request )
    {
        $rules = [
            'month'        => 'required',
            'upload_file'  => 'required',
            'pricing_type' => 'required',
        ];
        $this->validate( $request, $rules );

        DB::beginTransaction();
        try {
            $data = $this->service->store( array_merge( $request->all(), [ 'created_by' => auth()->user()->id, 'updated_by' => auth()->user()->id ] ) );
            DB::commit();

            debug_log( "Pricing created successfully!", $data );

            return api( $data )->success( 'Pricing Created successfully!', Response::HTTP_CREATED );
        } catch ( \Exception $e ) {
            debug_log( "Pricing create failed!", $e->getTrace() );
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
    public function show( $id, Request $request ): \Illuminate\Http\JsonResponse
    {
        $data = $this->service->getById( $id );
        $data = ( new PricingPresenter( $data ) )->get();

        return api( $data )->success( 'Success!' );
    }

    /**
     *
     * @param $id
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update( $id, Request $request ): \Illuminate\Http\JsonResponse
    {
        $rules = [
            'month'        => 'required',
            'pricing_type' => 'required',
        ];
        $this->validate( $request, $rules );

        DB::beginTransaction();
        try {
            $data = $this->service->update( $id, array_merge( $request->all(), [ 'updated_by' => auth()->user()->id ] ) );
            DB::commit();

            debug_log( "Pricing updated successfully!", $data );

            return api( $data )->success( 'Pricing Updated successfully!', Response::HTTP_CREATED );
        } catch ( \Exception $e ) {
            debug_log( "Pricing update failed!", $e->getTrace() );
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
    public function destroy( $id ): \Illuminate\Http\JsonResponse
    {
        try {
            $data = $this->service->destroy( $id );

            debug_log( "Pricing deleted successfully!", $data );

            return api( $data )->success( 'Pricing Deleted Successfully!' );
        } catch ( Exception $e ) {
            debug_log( "Pricing deletion failed!", $e->getTrace() );

            return api()->fails( $e->getMessage(), Response::HTTP_BAD_REQUEST );
        }
    }

    public function pricingFileUpload( Request $request )
    {
        $this->validate( $request, [
            'file' => 'required|mimes:pdf|max:10000',
        ] );

        $upload = app( FileManager::class )->upload( $request->file, 'uploads/pricing', str_replace(' ', '_', $request->file->getClientOriginalName()) );
        if ( !$upload ) {
            return response()->json( [ 'success' => 'false', 'data' => 'Failed to file upload' ] );
        }
        return response()->json( [ 'success' => 'true', 'data' => $upload ] );
    }
}


