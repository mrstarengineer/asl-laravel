<?php

namespace App\Http\Controllers\Api\V1\Notification;

use App\Http\Controllers\Controller;
use App\Presenters\PaginatorPresenter;
use App\Presenters\NotificationPresenter;
use App\Services\Notification\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;

class NotificationController extends Controller {
	private $service;

	public function __construct( NotificationService $service ) {
		$this->service = $service;
	}

	/**
	 * @param Request $request
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function index( Request $request ): \Illuminate\Http\JsonResponse {
		$data = $this->service->all( $request->all() )->toArray();
		$data = ( new PaginatorPresenter( $data ) )->presentBy( NotificationPresenter::class );

		return response()->json( $data );
	}

	/**
	 * @param Request $request
	 */
	public function store( Request $request ) {
        $this->validate( $request, [
            'subject' => 'required',
            'message' => 'required',
        ] );

        DB::beginTransaction();
        try {
            $userId = auth()->user()->id;
            $data = $this->service->store( array_merge( $request->all(), [ 'user_id' => $userId, 'read' => 0, 'created_by' => $userId, 'updated_by' => $userId ] ) );
            DB::commit();

            debug_log( "Notification created successfully!", $data );

            return api( $data )->success( 'Notification Created successfully!', Response::HTTP_CREATED );
        } catch ( \Exception $e ) {
			debug_log( "Notification create failed!", $e->getTrace() );
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
	public function show( $id, Request $request ): \Illuminate\Http\JsonResponse {
		$data = $this->service->getById( $id );
		$data = ( new NotificationPresenter( $data ) )->get();

		return api( $data)->success( 'Success!' );
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
        $this->validate( $request, [
            'subject' => 'required',
            'message' => 'required',
        ] );

        DB::beginTransaction();
        try {
            $userId = auth()->user()->id;
            $data = $this->service->update( $id, array_merge( $request->all(), [ 'user_id' => $userId, 'read' => 0, 'created_by' => $userId, 'updated_by' => $userId ] ) );
            DB::commit();

            debug_log( "Notification updated successfully!", $data );

            return api( $data )->success( 'Notification Updated successfully!', Response::HTTP_CREATED );
        } catch ( \Exception $e ) {
			debug_log( "Notification update failed!", $e->getTrace() );
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
	public function destroy( $id ): \Illuminate\Http\JsonResponse {
		try {
			$data = $this->service->destroy( $id );

			debug_log( "Notification deleted successfully!", $data );

			return api( $data )->success( 'Notification Deleted Successfully!' );
		} catch ( Exception $e ) {
			debug_log( "Notification deletion failed!", $e->getTrace() );

			return api()->fails( $e->getMessage(), Response::HTTP_BAD_REQUEST );
		}
	}
}


