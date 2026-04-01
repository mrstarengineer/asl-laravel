<?php

namespace App\Http\Controllers\Api\V1\Claim;

use App\Enums\ClaimPhotoType;
use App\Enums\ClaimType;
use App\Enums\ReadStatus;
use App\Enums\Roles;
use App\Http\Controllers\Controller;
use App\Models\ClaimImage;
use App\Presenters\PaginatorPresenter;
use App\Presenters\VehicleClaim;
use App\Services\Vehicle\VehicleClaimService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\Filesystem;
use League\Flysystem\ZipArchive\ZipArchiveAdapter;

class StorageClaimController extends Controller
{
    private $service;

    public function __construct ( VehicleClaimService $service )
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
        $data = $this->service->all( array_merge( $request->all(), [ 'type' => ClaimType::STORAGE_CLAIM ] ) )->toArray();

        $data = ( new PaginatorPresenter( $data ) )->presentBy( VehicleClaim::class );

        return response()->json( $data );
    }

    /**
     * @param Request $request
     */
    public function store ( Request $request )
    {
        $rules = [
            'vehicle_id'       => 'required|unique:App\Models\VehicleClaim',
            'customer_user_id' => 'required',
            'vehicle_part'     => 'required',
            'claim_amount'     => 'required',
        ];

        $this->validate( $request, $rules );

        DB::beginTransaction();
        try {
            $data = $this->service->store( array_merge( $request->all(), [ 'type' => ClaimType::STORAGE_CLAIM ] ) );
            DB::commit();

            debug_log( "Storage Claim created successfully!", $data );

            return api( $data )->success( 'Storage Claim Created successfully!', Response::HTTP_CREATED );
        } catch ( \Exception $e ) {
            debug_log( "Storage Claim create failed!", $e->getTrace() );
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

        if ( optional( auth()->user() )->role != Roles::CUSTOMER && $data->admin_view == ReadStatus::UNREAD ) {
            $data->update([ 'admin_view' => ReadStatus::READ ]);
        }

        $data = ( new VehicleClaim( $data ) )->get();
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

            debug_log( "Storage Claim updated successfully!", $data );

            return api( $data )->success( 'Storage Claim Updated successfully!', Response::HTTP_CREATED );
        } catch ( \Exception $e ) {
            debug_log( "Storage Claim update failed!", $e->getTrace() );
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

            debug_log( "Storage Claim deleted successfully!", $data );

            return api( $data )->success( 'Storage Claim Deleted Successfully!' );
        } catch ( \Exception $e ) {
            debug_log( "Storage Claim deletion failed!", $e->getTrace() );

            return api()->fails( $e->getMessage(), Response::HTTP_BAD_REQUEST );
        }
    }

    public function downloadPhotos ( $id, Request $request )
    {
        try {
            $type = $request->get( 'type', ClaimPhotoType::CUSTOMER_PHOTO );

            $allImages = ClaimImage::where( ['storage_claim_id' => $id, 'type' => $type] )->get();
            $v = $this->service->getById( $id )->vehicle->toArray();

            if ( $allImages->count() == 0 ) {
                return 'No Images Found';
            }

            $file = $v['vin'] . '-claim_' . $type == ClaimPhotoType::CUSTOMER_PHOTO ? 'customer' : 'admin' . '_photos.zip';
            $zipFileUrl = public_path( 'uploads/' . $file );

            if ( file_exists( $zipFileUrl ) ) {
                unlink( $zipFileUrl );
            }

            $zip = new Filesystem( new ZipArchiveAdapter( $zipFileUrl ) );

            foreach ( $allImages as $files ) {
                $zip->put( basename( $files->image ), file_get_contents( Storage::url( $files->image ) ) );
            }
            $zip->getAdapter()->getArchive()->close();

            if ( file_exists( $zipFileUrl ) ) {
                return response()->download( $zipFileUrl )->deleteFileAfterSend( true );
            }

            return 'Something went wrong.';
        } catch ( \Exception $e ) {
            throw new \Exception( 'Something went wrong.' );
        }
    }
}
