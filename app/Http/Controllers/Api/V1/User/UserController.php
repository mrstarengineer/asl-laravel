<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Enums\Roles;
use App\Enums\VisibilityStatus;
use App\Http\Controllers\Controller;
use App\Jobs\SyncSingleUserPermissionsJob;
use App\Models\Role;
use App\Models\User;
use App\Presenters\PaginatorPresenter;
use App\Presenters\UserPresenter;
use App\Services\User\UserService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Mockery\Exception;

class UserController extends Controller
{
    private $service;

    public function __construct( UserService $service )
    {
        $this->service = $service;
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index( Request $request ): \Illuminate\Http\JsonResponse
    {
        $currentUser = auth()->user();
        if ( $currentUser->role != Roles::MASTER_ADMIN && $currentUser->role != Roles::SUPER_ADMIN ) {
            return api()->fails('No access right', Response::HTTP_BAD_REQUEST);
        }
        $data = $this->service->all($request->all())->toArray();
        $data = ( new PaginatorPresenter($data) )->presentBy(UserPresenter::class);

        return response()->json($data);
    }

    /**
     * @param Request $request
     */
    public function store( Request $request )
    {
        $this->validate($request, [
            'email'    => 'required|email|unique:App\Models\User',
            'username' => 'required|unique:App\Models\User',
            'password' => 'required',
            'role'     => 'required',
        ]);

        DB::beginTransaction();
        try {
            $data = $this->service->store($request->all());
            DB::commit();

            // Sync user permissions when role permissions updated
            SyncSingleUserPermissionsJob::dispatch($request->role + 1, $data->id);
//            $rolePermissions = Role::find( $request->role + 1 )->permissions()->pluck( 'permission_id' )->toArray();
//            User::find( $data->id )->syncPermissions( $rolePermissions );

            debug_log("User created successfully!", $data);

            return api($data)->success('User Created successfully!', Response::HTTP_CREATED);
        } catch ( \Exception $e ) {
            debug_log("User create failed!", $e->getTrace());
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

        $data = ( new UserPresenter($data) )->get();
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
        $this->validate($request, [
            'email'    => [
                'required',
                'email',
                Rule::unique('users')->ignore($id)->whereNull('deleted_at'),
            ],
            'username' => [
                'required',
                Rule::unique('users')->ignore($id)->whereNull('deleted_at'),
            ],
            'role'     => 'required',
        ]);
        DB::beginTransaction();
        try {
            $data = $this->service->update($id, $request->all());
            DB::commit();

            SyncSingleUserPermissionsJob::dispatch($request->role + 1, $data->id);
//            $rolePermissions = Role::find( $request->role + 1 )->permissions()->pluck( 'permission_id' )->toArray();
//            User::find( $data->id )->syncPermissions( $rolePermissions );

            debug_log("User updated successfully!", $data);

            return api($data)->success('User Updated successfully!', Response::HTTP_CREATED);
        } catch ( \Exception $e ) {
            debug_log("User update failed!", $e->getTrace());
            DB::rollback();

            return api()->fails($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    public function changeStatus( $id )
    {
        try {
            $user = $this->service->getById($id);
            $data = $this->service->update($id, [
                'status'      => $user->status == VisibilityStatus::ACTIVE ? VisibilityStatus::INACTIVE : VisibilityStatus::ACTIVE,
                'inactive_at' => Carbon::now(),
            ]);
            DB::commit();

            debug_log("User status changed successfully!", $data);

            return api($data)->success('User status changed successfully.!', Response::HTTP_CREATED);
        } catch ( \Exception $e ) {
            debug_log("User update failed!", $e->getTrace());
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

            debug_log("User deleted successfully!", $data);

            return api($data)->success('User Deleted Successfully!');
        } catch ( Exception $e ) {
            debug_log("User deletion failed!", $e->getTrace());

            return api()->fails($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
}


