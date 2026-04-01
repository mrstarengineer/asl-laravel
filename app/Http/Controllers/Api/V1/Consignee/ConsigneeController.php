<?php

namespace App\Http\Controllers\Api\V1\Consignee;

use App\Enums\ActivityType;
use App\Exports\ConsigneesExport;
use App\Http\Controllers\Controller;
use App\Models\Consignee;
use App\Presenters\PaginatorPresenter;
use App\Presenters\ConsigneePresenter;
use App\Services\Consignee\ConsigneeService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Mockery\Exception;

class ConsigneeController extends Controller {
	private $service;

	public function __construct( ConsigneeService $service ) {
		$this->service = $service;
	}

	/**
	 * @param Request $request
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function index( Request $request ): \Illuminate\Http\JsonResponse {
		$data = $this->service->all( $request->all() )->toArray();
		$data = ( new PaginatorPresenter( $data ) )->presentBy( ConsigneePresenter::class );

		return response()->json( $data );
	}

	/**
	 * @param Request $request
	 */
	public function store( Request $request ) {
		$rules = [
			'customer_user_id'    => 'required',
			'consignee_name'      => 'required',
			'phone'               => 'required',
		];
		$this->validate($request, $rules);

		DB::beginTransaction();
		try {
			$data = $this->service->store( $request->all() );
            $logMessage = auth()->user()->username . ' has add a CONSIGNEE with name: ' . $request->consignee_name;
            store_activity( $logMessage, $request->all(), $data->id, ActivityType::CREATE );
			DB::commit();

			debug_log( "Consignee created successfully!", $data );

			return api( $data )->success( 'Consignee Created successfully!', Response::HTTP_CREATED );
		} catch ( \Exception $e ) {
			debug_log( "Consignee create failed!", $e->getTrace() );
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
        $data = $data ? ( new ConsigneePresenter( $data->toArray() ) )->get() : [];

        return api( $data )->success( 'Success!' );
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
            if( $request->version_id ) {
                $consignee = Consignee::findOrFail( $id );
                if( $consignee->version_id > $request->version_id ) {
                    return api()->fails( 'Data Already changed form another device, please refresh the page and try again.', Response::HTTP_BAD_REQUEST );
                }
            }

            $data = $this->service->update( $id, $request->all() );
            $logMessage = auth()->user()->username . ' has updated a CONSIGNEE with name: ' . $request->consignee_name;
            store_activity( $logMessage, $data->getChanges(), $data->id, ActivityType::UPDATE );
            DB::commit();

            debug_log( "Consignee updated successfully!", $data );

            return api( $data )->success( 'Consignee Updated successfully!', Response::HTTP_CREATED );
        } catch ( \Exception $e ) {
            debug_log( "Consignee update failed!", $e->getTrace() );
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
            $consignee = Consignee::find( $id );
            $data = $this->service->destroy( $id );
            $logMessage = auth()->user()->username . ' has DELETED a CUSTOMER with name: ' . $consignee->consignee_name;
            store_activity( $logMessage, $consignee->toArray(), $id, ActivityType::DELETE );

            debug_log( "Consignee deleted successfully!", $data );

            return api( $data )->success( 'Consignee Deleted Successfully!' );
        } catch ( Exception $e ) {
            debug_log( "Consignee deletion failed!", $e->getTrace() );

            return api()->fails( $e->getMessage(), Response::HTTP_BAD_REQUEST );
        }
    }

    public function search ( Request $request ): \Illuminate\Http\JsonResponse
    {
        $data = $this->service
            ->all( array_merge( $request->all(), [ 'limit' => -1, 'select' => [ 'id', 'consignee_name' ] ] ) );

        return response()->json( $data );
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportExcel ( Request $request ): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        return Excel::download( new ConsigneesExport( $request->all() ), 'consignees.xlsx' );
    }
}


