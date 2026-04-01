<?php

namespace App\Http\Controllers\Api\V1\Settings;

use App\Http\Controllers\Controller;
use App\Presenters\StatePresenter;
use App\Presenters\PaginatorPresenter;
use App\Services\State\StateService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;

class StateController extends Controller
{
    private $service;

    public function __construct ( StateService $service )
    {
        $this->service = $service;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index ( Request $request ): \Illuminate\Http\JsonResponse
    {
        $data = $this->service->all( $request->all() )->toArray();
        $data = ( new PaginatorPresenter( $data ) )->presentBy( StatePresenter::class );

        return response()->json( $data );
    }

    /**
     * @param Request $request
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store ( Request $request )
    {
        $this->validate( $request, [
            'name'       => 'required',
            'country_id' => 'required',
        ] );

        DB::beginTransaction();
        try {
            $data = $this->service->store( $request->all() );
            DB::commit();

            debug_log( "State created successfully!", $data );

            return api( $data )->success( 'Agent Created successfully!', Response::HTTP_CREATED );
        } catch ( \Exception $e ) {
            debug_log( "State create failed!", $e->getTrace() );
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
        $data = $this->service->getById( $id );

        return api( $data )->success( 'Success!' );
    }

    /**
     *
     * @param $id
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update ( $id, Request $request ): \Illuminate\Http\JsonResponse
    {
        $this->validate( $request, [
            'name'       => 'required',
            'country_id' => 'required',
        ] );

        DB::beginTransaction();
        try {
            $data = $this->service->update( $id, $request->all() );
            DB::commit();

            debug_log( "State updated successfully!", $data );

            return api( $data )->success( 'State Updated successfully!', Response::HTTP_CREATED );
        } catch ( \Exception $e ) {
            debug_log( "State update failed!", $e->getTrace() );
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

            debug_log( "State deleted successfully!", $data );

            return api( $data )->success( 'State Deleted Successfully!' );
        } catch ( Exception $e ) {
            debug_log( "State deletion failed!", $e->getTrace() );

            return api()->fails( $e->getMessage(), Response::HTTP_BAD_REQUEST );
        }
    }
}
