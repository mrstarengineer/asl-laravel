<?php

namespace App\Http\Controllers\Api\V1\Complain;

use App\Enums\ReadStatus;
use App\Enums\Roles;
use App\Http\Controllers\Controller;
use App\Presenters\ComplainPresenter;
use App\Presenters\PaginatorPresenter;
use App\Services\Complain\ComplainService;
use App\Services\Conversation\ConversationService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;

class ComplainController extends Controller
{
    protected $service;

    /**
     * @param ComplainService $service
     */
    public function __construct( ComplainService $service )
    {
        $this->service = $service;
    }

    /**
     * Get Complain list
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index ( Request $request )
    {
        $data = $this->service->all( $request->only( 'customer_user_id', 'status' ) )->toArray();
        $data = ( new PaginatorPresenter( $data ) )->presentBy( ComplainPresenter::class );

        return response()->json($data);
    }

    /**
     * Store new Complain
     *
     * @param Request $request
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'subject' => 'required',
            'message' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $data = $this->service->store( array_merge( $request->only('message', 'subject' ), [ 'customer_user_id' => auth()->user()->id ] ) );
            DB::commit();

            debug_log("Complain created successfully!", $data);

            return api($data)->success('Complain Created successfully!', Response::HTTP_CREATED);
        } catch (\Exception $e) {
            debug_log("Complain create failed!", $e->getTrace());
            DB::rollback();

            return api()->fails($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Get one Complain by id
     *
     * @param $id
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id, Request $request): \Illuminate\Http\JsonResponse
    {
        $complain = $this->service->getById( $id );
        if ( optional( auth()->user() )->role != Roles::CUSTOMER && $complain && $complain->read_by_admin == ReadStatus::UNREAD ) {
            $complain->update( [ 'read_by_admin' => ReadStatus::READ ] );
        }
        $data = ( new ComplainPresenter( $complain->toArray() ) )->get();
        $data['total_unread_complain'] = $this->service->adminUnreadCount();

        return api( $data )->success( 'Success!' );
    }

    /**
     * Update Complain
     *
     * @param $id
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update($id, Request $request): \Illuminate\Http\JsonResponse
    {
        $this->validate( $request, [
            'subject' => 'required',
            'message' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $data = $this->service->update($id, $request->all());
            DB::commit();

            debug_log("Complain updated successfully!", $data);

            return api($data)->success('Complain Updated successfully!', Response::HTTP_CREATED);
        } catch (\Exception $e) {
            debug_log("Complain update failed!", $e->getTrace());
            DB::rollback();

            return api()->fails($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Delete one Complain
     *
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id): \Illuminate\Http\JsonResponse
    {
        try {
            $data = $this->service->destroy($id);

            debug_log("Complain deleted successfully!", $data);

            return api($data)->success('Complain Deleted Successfully!');
        } catch (Exception $e) {
            debug_log("Complain deletion failed!", $e->getTrace());

            return api()->fails($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    public function storeConversation ( Request $request )
    {
        $this->validate( $request, [
            'message'  => 'required',
            'model_id' => 'required',
        ]);

        try {
            $data = app(ConversationService::class)->store( array_merge( $request->only( 'message', 'model_id' ), [ 'sender_id' => auth()->user()->id ] ) );

            debug_log("Conversation added successfully!", $data);

            return api($data)->success('Conversation added Successfully!');
        } catch (Exception $e) {
            debug_log("Conversation added failed!", $e->getTrace());

            return api()->fails($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
}
