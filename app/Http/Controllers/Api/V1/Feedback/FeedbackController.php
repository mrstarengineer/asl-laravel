<?php

namespace App\Http\Controllers\Api\V1\Feedback;

use App\Enums\ReadStatus;
use App\Enums\Roles;
use App\Http\Controllers\Controller;
use App\Presenters\FeedbackPresenter;
use App\Presenters\PaginatorPresenter;
use App\Services\Feedback\FeedbackService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;

class FeedbackController extends Controller
{
    protected $service;

    /**
     * @param FeedbackService $service
     */
    public function __construct( FeedbackService $service )
    {
        $this->service = $service;
    }

    /**
     * Get feedback list
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index ( Request $request )
    {
        $data = $this->service->all( $request->only( 'customer_user_id', 'status' ) )->toArray();
        $data = (new PaginatorPresenter($data))->presentBy(FeedbackPresenter::class);

        return response()->json($data);
    }

    /**
     * Store new Feedback
     *
     * @param Request $request
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'message' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $data = $this->service->store( array_merge( $request->only('message', 'note'), [ 'customer_user_id' => auth()->user()->id ] ) );
            DB::commit();

            debug_log("Feedback created successfully!", $data);

            return api($data)->success('Feedback Created successfully!', Response::HTTP_CREATED);
        } catch (\Exception $e) {
            debug_log("Feedback create failed!", $e->getTrace());
            DB::rollback();

            return api()->fails($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Get one Feedback by id
     *
     * @param $id
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id, Request $request): \Illuminate\Http\JsonResponse
    {
        $feedback = $this->service->getById($id);
        if ( optional( auth()->user() )->role != Roles::CUSTOMER && $feedback && $feedback->read_by_admin == ReadStatus::UNREAD ) {
            $feedback->update( [ 'read_by_admin' => ReadStatus::READ ] );
        }
        $data = ( new FeedbackPresenter( $feedback->toArray() ) )->get();
        $data['total_unread_feedback'] = $this->service->adminUnreadCount();

        return api($data)->success('Success!');
    }

    /**
     * Update Feedback
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
            'message' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $data = $this->service->update($id, $request->all());
            DB::commit();

            debug_log("Feedback updated successfully!", $data);

            return api($data)->success('Feedback Updated successfully!', Response::HTTP_CREATED);
        } catch (\Exception $e) {
            debug_log("Feedback update failed!", $e->getTrace());
            DB::rollback();

            return api()->fails($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Delete one Feedback
     *
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id): \Illuminate\Http\JsonResponse
    {
        try {
            $data = $this->service->destroy($id);

            debug_log("Feedback deleted successfully!", $data);

            return api($data)->success('Feedback Deleted Successfully!');
        } catch (Exception $e) {
            debug_log("Feedback deletion failed!", $e->getTrace());

            return api()->fails($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
}
