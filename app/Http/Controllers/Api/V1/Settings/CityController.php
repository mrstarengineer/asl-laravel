<?php

namespace App\Http\Controllers\Api\V1\Settings;

use App\Http\Controllers\Controller;
use App\Presenters\CityPresenter;
use App\Presenters\PaginatorPresenter;
use App\Services\City\CityService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;

class CityController extends Controller
{
    private $service;

    public function __construct(CityService $service)
    {
        $this->service = $service;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        $data = $this->service->all($request->all())->toArray();
        $data = (new PaginatorPresenter($data))->presentBy(CityPresenter::class);

        return response()->json($data);
//        return api($data)->success('Success!');
    }

    /**
     * @param Request $request
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $data = $this->service->store($request->all());
            DB::commit();

            debug_log("City created successfully!", $data);

            return api($data)->success('City Created successfully!', Response::HTTP_CREATED);
        } catch (\Exception $e) {
            debug_log("City create failed!", $e->getTrace());
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
    public function show($id, Request $request): \Illuminate\Http\JsonResponse
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
    public function update($id, Request $request): \Illuminate\Http\JsonResponse
    {
        DB::beginTransaction();
        try {
            $data = $this->service->update($id, $request->all());
            DB::commit();

            debug_log("City updated successfully!", $data);

            return api($data)->success('City Updated successfully!', Response::HTTP_CREATED);
        } catch (\Exception $e) {
            debug_log("City update failed!", $e->getTrace());
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
    public function destroy($id): \Illuminate\Http\JsonResponse
    {
        try {
            $data = $this->service->destroy($id);

            debug_log("City deleted successfully!", $data);

            return api($data)->success('City Deleted Successfully!');
        } catch (Exception $e) {
            debug_log("City deletion failed!", $e->getTrace());

            return api()->fails($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
}
