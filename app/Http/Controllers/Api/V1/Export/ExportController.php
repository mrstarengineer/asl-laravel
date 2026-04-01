<?php

namespace App\Http\Controllers\Api\V1\Export;

use App\Enums\ActivityType;
use App\Enums\ExportPhotoType;
use App\Enums\VehicleStatus;
use App\Enums\VisibilityStatus;
use App\Exports\ContainerExport;
use App\Http\Controllers\Controller;
use App\Models\Consignee;
use App\Models\Customer;
use App\Models\Export;
use App\Models\ExportImage;
use App\Models\StreamshipLine;
use App\Models\Vehicle;
use App\Presenters\ExportDetailPresenter;
use App\Presenters\PaginatorPresenter;
use App\Presenters\ExportPresenter;
use App\Presenters\VehiclePresenter;
use App\Services\Export\ExportService;
use App\Services\Storage\FileManager;
use App\Services\Vehicle\VehicleService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use League\Flysystem\Filesystem;
use League\Flysystem\ZipArchive\ZipArchiveAdapter;
use PDF;
use Mockery\Exception;
use ZipArchive;

class ExportController extends Controller
{
    private $service;

    public function __construct( ExportService $service )
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
        $data = $this->service->all( $request->all() )->toArray();

        $data = ( new PaginatorPresenter( $data ) )->presentBy( ExportPresenter::class );
        $data['yard_show']  =  (new VehicleService())->isShowYard();
        return response()->json( $data );
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function containers( Request $request ): \Illuminate\Http\JsonResponse
    {
        $data = $this->service->all( $request->all() );
        $data = ( new PaginatorPresenter( $data->toArray() ) )->presentBy( ExportPresenter::class );

        return response()->json( $data );
    }

    /**
     * @param Request $request
     */
    public function store( Request $request )
    {
        $rules = [
            'vehicle_ids'       => 'required|array',
            'customer_user_id'  => 'required',
            'booking_number'    => 'required',
            'ar_number'         => [ 'required', Rule::unique('exports', 'ar_number')->whereNull('deleted_at') ],
            'loading_date'      => 'nullable|date_format:Y-m-d',
            'eta'               => 'nullable|date_format:Y-m-d',
            'export_date'       => 'nullable|date_format:Y-m-d',
            'vessel'            => 'required',
            'terminal'          => 'required',
            'streamship_line'   => 'required',
            'destination'       => 'required',
            'container_type'    => 'required',
            'port_of_discharge' => 'required',
            'port_of_loading'   => 'required',
            'consignee'         => 'required',
            'shipper_id'        => 'required',
        ];

        if ( $request->loading_date && $request->export_date ) {
            $rules[ 'export_date' ] = 'required|date_format:Y-m-d|after:loading_date';
        }
        if ( $request->eta && $request->export_date ) {
            $rules[ 'eta' ] = 'required|date_format:Y-m-d|after:export_date';
        }

        $this->validate( $request, $rules );

        $vehicleIds = $request->get( 'vehicle_ids', [] );
        $vehicleLocationCount = Vehicle::whereIn( 'id', $vehicleIds )->distinct()->pluck( 'location_id' )->count();
        if ( $vehicleLocationCount !== 1 ) {
            return api()->fails( 'Multiple location vehicle not allowed in one container.', Response::HTTP_BAD_REQUEST );
        }

        DB::beginTransaction();
        try {
            $data = $this->service->store( $request->all() );
            $logMessage = auth()->user()->username . ' has ADDED an EXPORT with EXPORT_ID: ' . $data->id . ', ar number: ' . $data->ar_number . ' and Container Number: ' . $data->container_number;
            store_activity( $logMessage, $data->toArray(), $data->id, ActivityType::CREATE );
            DB::commit();

            debug_log( "Export created successfully!", $data );

            return api( $data )->success( 'Export Created successfully!', Response::HTTP_CREATED );
        } catch ( \Exception $e ) {
            debug_log( "Export create failed!", $e->getTrace() );
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
    public function show( $id, Request $request ): \Illuminate\Http\JsonResponse
    {
        $data = $this->service->getById( $id );
        $response[ 'export_details' ] = ( new ExportDetailPresenter( $data->toArray() ) )->get();
        $response[ 'vehicles' ] = ( new VehiclePresenter( app( VehicleService::class )->all( [ 'export_id' => $id, 'limit' => -1 ] )->toArray() ) )->get();
        $response['yard_show']  = (new VehicleService())->isShowYard();
        return api( $response )->success( 'Success!' );
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
        $rules = [
            'vehicle_ids'       => 'required|array',
            'customer_user_id'  => 'required',
            'booking_number'    => 'required',
            'ar_number'         => [ 'required', Rule::unique('exports', 'ar_number')->whereNull('deleted_at')->ignore($id) ],
            'loading_date'      => 'nullable|date_format:Y-m-d',
            'eta'               => 'nullable|date_format:Y-m-d',
            'export_date'       => 'nullable|date_format:Y-m-d',
            'vessel'            => 'required',
            'terminal'          => 'required',
            'streamship_line'   => 'required',
            'destination'       => 'required',
            'container_type'    => 'required',
            'port_of_discharge' => 'required',
            'port_of_loading'   => 'required',
            'consignee'         => 'required',
            'shipper_id'        => 'required',
        ];
        if ( $request->loading_date && $request->export_date ) {
            $rules[ 'export_date' ] = 'required|date_format:Y-m-d|after:loading_date';
        }
        if ( $request->eta && $request->export_date ) {
            $rules[ 'eta' ] = 'required|date_format:Y-m-d|after:export_date';
        }
        $this->validate( $request, $rules );

        DB::beginTransaction();
        try {
            if ( $request->version_id ) {
                $export = Export::findOrFail( $id );
                if ( $export->version_id > $request->version_id ) {
                    DB::rollBack();
                    return api()->fails( 'Data Already changed form another device, please refresh the page and try again.', Response::HTTP_BAD_REQUEST );
                }
            }
            $data = $this->service->update( $id, $request->all() );
            $logMessage = auth()->user()->username . ' has UPDATED an EXPORT with EXPORT_ID: ' . $data->id . ', ar number: ' . $data->ar_number . ' and Container Number: ' . $data->container_number;
            store_activity( $logMessage, $data->getChanges(), $data->id, ActivityType::UPDATE );
            DB::commit();

            debug_log( "Export updated successfully!", $data );

            return api( $data )->success( 'Export Updated successfully!', Response::HTTP_CREATED );
        } catch ( \Exception $e ) {
            debug_log( "Export update failed!", $e->getTrace() );
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
    public function destroy( $id ): \Illuminate\Http\JsonResponse
    {
        try {
            $export = Export::find( $id );
            $data = $this->service->destroy( $id );
            $logMessage = auth()->user()->username . ' has DELETED an EXPORT with EXPORT_ID: ' . $export->id . ', ar number: ' . $export->ar_number . ' and Container Number: ' . $export->container_number;
            store_activity( $logMessage, $export->toArray(), $export->id, ActivityType::DELETE );

            debug_log( "Export deleted successfully!", $data );

            return api( $data )->success( 'Export Deleted Successfully!' );
        } catch ( Exception $e ) {
            debug_log( "Export deletion failed!", $e->getTrace() );

            return api()->fails( $e->getMessage(), Response::HTTP_BAD_REQUEST );
        }
    }

    public function searchContainers( Request $request ): \Illuminate\Http\JsonResponse
    {
        return response()->json( $this->service->getContainerList( $request->all() ) );
    }

    public function addMoreImage( $id, Request $request )
    {
        try {
            foreach ( data_get( $request->fileUrls, [] ) as $url ) {
                if ( filter_var( $url, FILTER_VALIDATE_URL ) ) {
                    $uri = $uri = str_replace( env( 'AWS_S3_BASE_URL' ), '', $url );
                    $thumbnailFileName = str_replace( basename( $uri ), 'thumb-' . basename( $uri ), $uri );
                    $thumbnail = Storage::exists( $thumbnailFileName ) ? $thumbnailFileName : $uri;

                    $img = new ExportImage();
                    $img->name = $uri;
                    $img->thumbnail = $thumbnail;
                    $img->export_id = $id;
                    $img->type = $request->get( 'type', ExportPhotoType::EXPORT_PHOTO );
                    $img->save();
                }
            }

            debug_log( "Export image upload successfully!", $img );
            return response()->json( [ 'code' => 1, 'data' => 'Successfully uploaded images' ] );
        } catch ( Exception $e ) {
            return api()->fails( $e->getMessage(), Response::HTTP_BAD_REQUEST );
        }
    }

    public function nonHazModal( $id )
    {
        $export = $this->service->getById( $id );

        return view( 'modals.nonhaz_modal', compact( 'export' ) );
    }

    public function dockReceiptModal( $id )
    {
        $export = Export::with( [ 'customer', 'exporter.state', 'exporter.city', 'dock_receipt', 'houstan_custom_cover_letter.consignee_item', 'houstan_custom_cover_letter.notify_party_item.country', 'houstan_custom_cover_letter.notify_party_item.state', 'vehicles' ] )->findOrFail( $id );

        return view( 'modals.dock_receipt_modal', compact( 'export' ) );
    }

    public function hostonCustomCoverLetterModal( $id )
    {
        $export = Export::with( [ 'customer', 'exporter.state', 'exporter.city', 'houstan_custom_cover_letter.notify_party_item.city', 'houstan_custom_cover_letter.notify_party_item.country', 'houstan_custom_cover_letter.notify_party_item.state', 'vehicles.towing_request', 'vehicles.location' ] )->findOrFail( $id );

        return view( 'modals.houstan_cover_letter_modal', compact( 'export' ) );
    }

    public function customCoverLetterModal( $id )
    {
        $expoter_info = Customer::where( 'company_name', 'LA' )->first();
        $export = Export::with( [ 'customer', 'exporter.state', 'exporter.city', 'vehicles.towing_request' ] )->findOrFail( $id );

        return view( 'modals.custom_cover_letter_modal', compact( 'export', 'expoter_info' ) );
    }

    public function landingModal( $id, Request $request )
    {
        $export = Export::with( [ 'customer', 'exporter.state', 'exporter.city', 'houstan_custom_cover_letter.consignee_item.city', 'houstan_custom_cover_letter.consignee_item.state', 'houstan_custom_cover_letter.consignee_item.country', 'houstan_custom_cover_letter.notify_party_item.country', 'houstan_custom_cover_letter.notify_party_item.state', 'houstan_custom_cover_letter.notify_party_item.city', 'vehicles' ] )->findOrFail( $id );
        $data_consignee = Consignee::where( 'consignee_name', 'AMAYA SHIPPING LINE LLC' )->first();

        return view( 'modals.landing_modal', compact( 'export', 'data_consignee' ) );
    }

    public function manifestModal( $id, Request $request )
    {
        $export = Export::with( [ 'customer.state', 'exporter.state', 'exporter.city', 'houstan_custom_cover_letter.consignee_item.country', 'houstan_custom_cover_letter.notify_party_item', 'vehicles' ] )->findOrFail( $id );

        return view( 'modals.manifest_modal', compact( 'export' ) );
    }

    public function manifestPdf( $id, Request $request )
    {
        ini_set( 'memory_limit', '2000M' );
        set_time_limit( 0 );

        $export = Export::with( [ 'customer', 'exporter.state', 'exporter.city', 'houstan_custom_cover_letter.consignee_item.city', 'houstan_custom_cover_letter.consignee_item.state', 'houstan_custom_cover_letter.consignee_item.country', 'houstan_custom_cover_letter.notify_party_item.country', 'houstan_custom_cover_letter.notify_party_item.state', 'houstan_custom_cover_letter.notify_party_item.city', 'vehicles' ] )->findOrFail( $id );
        $pdf = PDF::loadView( 'pdf.manifest', [ 'export' => $export ] );

        return $pdf->stream( 'manifest_report.pdf' );
    }

    public function docReceivedPdf( $id )
    {
        ini_set( 'memory_limit', '2000M' );
        set_time_limit( 0 );

        $export = Export::with( [ 'customer', 'exporter.state', 'exporter.city', 'dock_receipt', 'houstan_custom_cover_letter.consignee_item', 'houstan_custom_cover_letter.notify_party_item.country', 'houstan_custom_cover_letter.notify_party_item.state', 'vehicles' ] )->findOrFail( $id );

        $pdf = PDF::loadView( 'pdf.doc_receipt', [ 'export' => $export ] );

        return $pdf->stream( 'manifest_report.pdf' );
    }

    public function landingPdf( $id, Request $request )
    {
        ini_set( 'memory_limit', '2000M' );
        set_time_limit( 0 );

        $export = Export::with( [ 'customer', 'exporter.state', 'exporter.city', 'houstan_custom_cover_letter.consignee_item.city', 'houstan_custom_cover_letter.consignee_item.state', 'houstan_custom_cover_letter.consignee_item.country', 'houstan_custom_cover_letter.notify_party_item.country', 'houstan_custom_cover_letter.notify_party_item.state', 'houstan_custom_cover_letter.notify_party_item.city', 'vehicles' ] )->findOrFail( $id );
        $pdf = PDF::loadView( 'pdf.landing', [ 'export' => $export, ] );

        return $pdf->stream( $export->ar_number . '.pdf' );
    }

    public function downloadPhotos( $id, Request $request )
    {
        try {
            $type = $request->get( 'type', ExportPhotoType::EXPORT_PHOTO );

            $allImages = ExportImage::where( [ 'export_id' => $id, 'type' => $type ] )->get();
            $export = $this->service->getById( $id );

            if ( $allImages->count() == 0 ) {
                return 'No Images Found';
            }

            $file = $export->container_number . '-' . trans( 'exports.photos_types.' . $type ) . '.zip';
            $zipFileUrl = public_path( 'uploads/' . $file );

            if ( file_exists( $zipFileUrl ) ) {
                unlink( $zipFileUrl );
            }

            $zip = new Filesystem( new ZipArchiveAdapter( $zipFileUrl ) );
            foreach ( $allImages as $files ) {
                if ( Storage::exists( $files->name ) ) {
                    $zip->put( basename( $files->name ), file_get_contents( Storage::url( $files->name ) ) );
                }
            }
            $zip->getAdapter()->getArchive()->close();

            if ( file_exists( $zipFileUrl ) ) {
                return response()->download( $zipFileUrl )->deleteFileAfterSend( true );
            }

            throw new \Exception( 'Something went wrong.' );
        } catch ( \Exception $e ) {
            dd( $e->getMessage() );
        }
    }

    public function uploadExportImage( $id, Request $request )
    {
        $upload = app( FileManager::class )->uploadImageWithThumbnail( $request->file, 'uploads/exports/images/' . $id );
        if ( !$upload ) {
            return response()->json( [ 'success' => 'false', 'data' => 'Failed to file upload' ], Response::HTTP_UNPROCESSABLE_ENTITY );
        }

        return response()->json( [ 'success' => 'true', 'data' => $upload ] );
    }

    public function uploadExportDocument( $id, Request $request )
    {
        $upload = app( FileManager::class )->upload( $request->file, 'uploads/exports/documents/' . $id );
        if ( !$upload ) {
            return response()->json( [ 'success' => 'false', 'data' => 'Failed to file upload' ], Response::HTTP_UNPROCESSABLE_ENTITY );
        }

        return response()->json( [ 'success' => 'true', 'data' => $upload ] );
    }

    /*
    * @param $id
    * @param Request $request
    *
    * @return \Illuminate\Http\JsonResponse
    */
    public function search( Request $request ): \Illuminate\Http\JsonResponse
    {
        $data = $this->service->search( $request->all() );
        $response = ( new ExportDetailPresenter( $data->toArray() ) )->get();
        if ( !empty( $data[ 'id' ] ) ) {
            $response[ 'vehicles' ] = ( new VehiclePresenter( app( VehicleService::class )->all( [ 'export_id' => $data->id, 'limit' => -1 ] )->toArray() ) )->get();
        }

        return api( $response )->success( 'Success!' );
    }

    public function getStreamshipLines( Request $request )
    {
        return Cache::rememberForever( 'streamship_lines', function () {
            StreamshipLine::select( 'name' )->where( 'status', VisibilityStatus::ACTIVE )->pluck( 'name' );
        } );
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportExcel( Request $request )
    {
        ini_set( 'memory_limit', '2000M' );
        set_time_limit( 0 );

        if ( $request->auth_user_id ) {
            force_login( $request->auth_user_id );
        }

        return ( new ContainerExport( $request->all() ) )->download( 'exports.xlsx', \Maatwebsite\Excel\Excel::XLSX );
    }

    public function getTrackingUrl( $containerNo )
    {
        $container = Export::where( 'container_number', urldecode( $containerNo ) )->first();

        if ( empty( $container ) ) {
            return response()->json( [ 'success' => 'true', 'message' => 'Container not exists in the system.' ] );
        }

        $url = $this->service->trackingUrl( $containerNo );

        if ( empty( $url ) ) {
            return response()->json( [ 'success' => 'true', 'message' => 'Sorry! No tracking url found.' ] );
        }

        return response()->json( [ 'success' => 'true', 'url' => $url ] );
    }

    public function saveContainerNote( $id, Request $request )
    {
        Export::where( 'id', $id )->update( [ 'note' => $request->note ?? '' ] );

        return response()->json( [ 'success' => 'true', 'message' => 'Note saved successfully' ] );
    }

    /**
     * Handover Container
     *
     * @param $id
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function handOver( $id, Request $request ): \Illuminate\Http\JsonResponse
    {
        $this->validate( $request, [ 'handed_over_date' => 'required' ] );

        try {
            $export = Export::where( 'status', VehicleStatus::ARRIVED )->find( $id );
            if ( empty( $export ) ) {
                return response()->json( [ 'success' => false, 'message' => 'Export not found in Arrived Status.' ], 400 );
            }

            $data = $export->update( $request->only( [ 'handed_over_date' ] ) );
            Vehicle::where( 'export_id', $id )->update( [ 'status' => VehicleStatus::HANDED_OVER, 'handed_over_date' => $request->handed_over_date ] );

            debug_log( "Export updated successfully!" );

            return api( $data )->success( 'Export Updated successfully!' );
        } catch ( \Exception $e ) {
            debug_log( "Export update failed! (" . $e->getMessage() . ")", $e->getTrace() );

            return api()->fails( $e->getMessage() );
        }
    }

    public function deletePhoto( $id )
    {
        try {
            ExportImage::where( 'id', $id )->delete();

            return api()->success( 'Export Image deleted successfully!' );
        } catch ( \Exception $e ) {
            return api()->fails( 'Export Image delete failed!' );
        }
    }

    public function deleteAllPhotos( $id, Request $request )
    {
        $request->validate([
            'type' => 'required|integer|between:1,4',
        ]);

        try {
            ExportImage::where( [
                'export_id' => $id,
                'type' => $request->type
            ] )->delete();

            return api()->success( 'Export Images deleted successfully!' );
        } catch ( \Exception $e ) {
            return api()->fails( 'Export Images delete failed!' );
        }
    }

}


