<?php

namespace App\Http\Controllers\Api\V1\Settings;

use App\Http\Controllers\Controller;
use App\Presenters\CountryPresenter;
use App\Presenters\PaginatorPresenter;
use App\Services\Country\CountryService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;

class CountryController extends Controller
{
    private $service;

    public function __construct(CountryService $service)
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
        $data = (new PaginatorPresenter($data))->presentBy(CountryPresenter::class);

        return response()->json($data);
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

            debug_log("Country created successfully!", $data);

            return api($data)->success('Country Created successfully!', Response::HTTP_CREATED);
        } catch (\Exception $e) {
            debug_log("Country create failed!", $e->getTrace());
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
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update($id, Request $request): \Illuminate\Http\JsonResponse
    {
        $this->validate($request, [
            'name' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $data = $this->service->update($id, $request->all());
            DB::commit();

            debug_log("Country updated successfully!", $data);

            return api($data)->success('Country Updated successfully!', Response::HTTP_CREATED);
        } catch (\Exception $e) {
            debug_log("Country update failed!", $e->getTrace());
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

            debug_log("Country deleted successfully!", $data);

            return api($data)->success('Country Deleted Successfully!');
        } catch (Exception $e) {
            debug_log("Country deletion failed!", $e->getTrace());

            return api()->fails($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
}
