<?php

namespace App\Http\Controllers\Api\V1\Page;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Presenters\PagePresenter;
use App\Presenters\PaginatorPresenter;
use App\Services\Page\PageService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;

class PageController extends Controller
{
    private $service;

    public function __construct( PageService $service )
    {
        $this->service = $service;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index( Request $request ): \Illuminate\Http\JsonResponse
    {
        $data = $this->service->all($request->all())->toArray();
        $data = ( new PaginatorPresenter($data) )->presentBy(PagePresenter::class);

        return response()->json($data);
    }

    /**
     * @param Request $request
     */
    public function store( Request $request )
    {
        $this->validate($request, [
            'title' => 'required',
            'content' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $data = $this->service->store($request->all());
            DB::commit();

            debug_log("Page created successfully!", $data);

            return api($data)->success('Page Created successfully!', Response::HTTP_CREATED);
        } catch ( \Exception $e ) {
            debug_log("Page create failed!", $e->getTrace());
            DB::rollback();

            return api()->fails($e->getMessage(), Response::HTTP_BAD_REQUEST);
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
        $data = $this->service->getById($id);

        return api($data)->success('Success!');
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
        DB::beginTransaction();
        try {
            $data = $this->service->update($id, $request->all());
            DB::commit();

            debug_log("Page updated successfully!", $data);

            return api($data)->success('Page Updated successfully!', Response::HTTP_CREATED);
        } catch ( \Exception $e ) {
            debug_log("Page update failed!", $e->getTrace());
            DB::rollback();

            return api()->fails($e->getMessage(), Response::HTTP_BAD_REQUEST);
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
            $data = $this->service->destroy($id);

            debug_log("Page deleted successfully!", $data);

            return api($data)->success('Page Deleted Successfully!');
        } catch ( Exception $e ) {
            debug_log("Page deletion failed!", $e->getTrace());

            return api()->fails($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    public function showPages( $slug )
    {
        return Page::where('slug', $slug)->first();
    }
}

