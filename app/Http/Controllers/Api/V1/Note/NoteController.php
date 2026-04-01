<?php

namespace App\Http\Controllers\Api\V1\Note;

use App\Enums\NoteStatus;
use App\Enums\Roles;
use App\Http\Controllers\Controller;
use App\Models\Note;
use App\Models\Vehicle;
use App\Presenters\PaginatorPresenter;
use App\Presenters\NotePresenter;
use App\Services\Note\NoteService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;

class NoteController extends Controller {
	private $service;

	public function __construct( NoteService $service ) {
		$this->service = $service;
	}

	/**
	 * @param Request $request
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function index( Request $request ): \Illuminate\Http\JsonResponse {
        $vehicleId = $request->get( 'vehicle_id' );
        if ( $vehicleId ) {
            $column = optional( auth()->user() )->role == Roles::CUSTOMER ? 'cust_view' : 'admin_view';
            Note::where( 'vehicle_id', $vehicleId )->update( [ $column => NoteStatus::READ ] );
        }

		$data = $this->service->all( $request->all() )->toArray();
		$data = ( new PaginatorPresenter( $data ) )->presentBy( NotePresenter::class );

		return response()->json( $data );
	}

	/**
	 * @param Request $request
	 */
	public function store( Request $request ) {
		DB::beginTransaction();
		try {
            $requestData = $request->all();
            $requestData['created_by'] = optional( auth()->user() )->id;
            if ( optional(auth()->user())->role == Roles::CUSTOMER ) {
                $requestData['cust_view'] = NoteStatus::READ;
            } else {
                $requestData['admin_view'] = NoteStatus::READ;
            }
			$data = $this->service->store( $requestData );
			DB::commit();

            if ( $vehicleId = $request->get('vehicle_id') ) {
                Vehicle::where( 'id', $vehicleId )->update( [ 'notes_status' => NoteStatus::OPEN ] );
            }

			debug_log( "Note created successfully!", $data );

			return api( $data )->success( 'Note Created successfully!', Response::HTTP_CREATED );
		} catch ( \Exception $e ) {
			debug_log( "Note create failed!", $e->getTrace() );
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

		$data = ( new NotePresenter( $data ) )->get();
		return api( $data)->success( 'Success!' );
	}

	/**
	 *
	 * @param $id
	 * @param Request $request
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function update( $id, Request $request ): \Illuminate\Http\JsonResponse {
		DB::beginTransaction();
		try {
			$data = $this->service->update( $id, $request->all() );
			DB::commit();

			debug_log( "Note updated successfully!", $data );

			return api( $data )->success( 'Note Updated successfully!', Response::HTTP_CREATED );
		} catch ( \Exception $e ) {
			debug_log( "Note update failed!", $e->getTrace() );
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

			debug_log( "Note deleted successfully!", $data );

			return api( $data )->success( 'Note Deleted Successfully!' );
		} catch ( Exception $e ) {
			debug_log( "Note deletion failed!", $e->getTrace() );

			return api()->fails( $e->getMessage(), Response::HTTP_BAD_REQUEST );
		}
	}
}


