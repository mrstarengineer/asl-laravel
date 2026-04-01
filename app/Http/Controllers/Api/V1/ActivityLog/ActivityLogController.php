<?php

namespace App\Http\Controllers\Api\V1\ActivityLog;

use App\Enums\VisibilityStatus;
use App\Http\Controllers\Controller;
use App\Presenters\ActivityLogDetailPresenter;
use App\Presenters\ActivityLogPresenter;
use App\Presenters\PaginatorPresenter;
use App\Presenters\UserPresenter;
use App\Services\ActivityLog\ActivityLogService;
use App\Services\User\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Mockery\Exception;

class ActivityLogController extends Controller
{
    private $service;

    public function __construct ( ActivityLogService $service )
    {
        $this->service = $service;
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index ( Request $request ): \Illuminate\Http\JsonResponse
    {
        $data = $this->service->all( $request->all() )->toArray();
        $data = ( new PaginatorPresenter( $data ) )->presentBy( ActivityLogPresenter::class );

        return response()->json( $data );
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

        $data = ( new ActivityLogDetailPresenter( $data ) )->get();
        return api( $data )->success( 'Success!' );
    }
}


