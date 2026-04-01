<?php

namespace App\Http\Controllers\Api\V1\Vehicle;

use App\Enums\ActivityType;
use App\Enums\Roles;
use App\Enums\VehicleDocumentType;
use App\Enums\VehiclePhotoType;
use App\Exports\VehiclesExport;
use App\Http\Controllers\Controller;
use App\Http\Resources\VehicleTrackingResource;
use App\Models\Condition;
use App\Models\Feature;
use App\Models\Vehicle;
use App\Models\VehicleDocument;
use App\Models\VehicleImage;
use App\Presenters\NotePresenter;
use App\Presenters\PaginatorPresenter;
use App\Presenters\VehicleDetailPresenter;
use App\Presenters\VehiclePresenter;
use App\Services\Location\LocationService;
use App\Services\Note\NoteService;
use App\Services\Storage\FileManager;
use App\Services\Vehicle\VehicleService;
use App\Services\VehicleWeight\VehicleWeightService;
use App\Services\Yard\YardService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use League\Flysystem\Filesystem;
use League\Flysystem\ZipArchive\ZipArchiveAdapter;
use Maatwebsite\Excel\Facades\Excel;
use Mockery\Exception;
use PDF;

class VehicleController extends Controller
{
    private $service;

    public function __construct( VehicleService $service )
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
        $filters = $request->all();
        if ( !empty( $filters[ 'status' ] ) ) {
            $filters[ 'status' ] = explode( ',', $filters[ 'status' ] );
        }
        $data = $this->service->all( $filters )->toArray();

        $data = ( new PaginatorPresenter( $data ) )->presentBy( VehiclePresenter::class );
        $data[ 'notes_count' ] = app( NoteService::class )->getUnreadCount();
        $data[ 'yard_show' ] = $this->service->isShowYard();
        $data[ 'yard_items' ] = app( YardService::class )->all( [ 'status' => 1, 'location_id' => $request->location ] );

        return response()->json( $data );
    }

    /**
     * @param Request $request
     */
    public function store( Request $request )
    {
        $rules = [
            'customer_user_id'    => 'required',
            'vin'                 => [
                'required',
                Rule::unique( 'vehicles' )->whereNull( 'deleted_at' ),
            ],
            'lot_number'          => [
                'required',
                Rule::unique( 'vehicles' )->whereNull( 'deleted_at' ),
            ],
            'year'                => 'required',
            'make'                => 'required',
            'model'               => 'required',
            'location_id'         => 'required',
            'yard_id'             => 'required',
            'towing_request_date' => 'required',
            'value'               => 'nullable|numeric',
        ];

        $this->validate( $request, $rules );

        DB::beginTransaction();
        try {
            $data = $this->service->store( $request->all() );
            DB::commit();
            $logMessage = auth()->user()->username . ' has ADDED a VEHICLE with VEHICLE_ID: ' . $data->id . ', vin: ' . $data->vin . ' and Lot Number: ' . $data->lot_number;
            store_activity( $logMessage, $data->toArray(), $data->id, ActivityType::CREATE );

            debug_log( "Vehicle created successfully!", $data );

            return api( $data )->success( 'Vehicle Created successfully!', Response::HTTP_CREATED );
        } catch ( \Exception $e ) {
            debug_log( "Vehicle create failed!", $e->getTrace() );
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
        $data = $this->service->getById( $id )->toArray();
        $data = ( new VehicleDetailPresenter( $data ) )->get();
        $data[ 'yard_show' ] = $this->service->isShowYard();
        return api( $data )->success( 'Success!' );
    }

    /**
     *
     * @param $id
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchByVin( Request $request ): \Illuminate\Http\JsonResponse
    {
        $data = $this->service->getByVin( $request->get( 'q' ) ) ?? collect( [] );
        $data = ( new VehicleDetailPresenter( $data->toArray() ) )->get();

        return api( $data )->success( 'Success!' );
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
            'customer_user_id'    => 'required',
            'vin'                 => [
                'required',
                Rule::unique( 'vehicles' )->ignore( $id )->whereNull( 'deleted_at' ),
            ],
            'status'              => 'required',
            'lot_number'          => [
                'required',
                Rule::unique( 'vehicles' )->ignore( $id )->whereNull( 'deleted_at' ),
            ],
            'year'                => 'required',
            'make'                => 'required',
            'model'               => 'required',
            'location_id'         => 'required',
            'version_id'          => 'required',
            'towing_request_date' => 'required',
            'value'               => 'nullable|numeric',
        ];
        $this->validate( $request, $rules );

        DB::beginTransaction();
        try {
            if ( $request->version_id ) {
                $vehicle = Vehicle::findOrFail( $id );
                if ( $vehicle->version_id > $request->version_id ) {
                    return api()->fails( 'Data Already changed form another device, please refresh the page and try again.', Response::HTTP_BAD_REQUEST );
                }
            }
            $data = $this->service->update( $id, $request->all() );
            DB::commit();
            $logMessage = auth()->user()->username . ' has UPDATED a VEHICLE with VEHICLE_ID: ' . $data->id . ', vin: ' . $data->vin . ' and Lot Number: ' . $data->lot_number;
            store_activity( $logMessage, $data->getChanges(), $id, ActivityType::UPDATE );

            debug_log( "Vehicle updated successfully!", $data );

            return api( $data )->success( 'Vehicle Updated successfully!', Response::HTTP_CREATED );
        } catch ( \Exception $e ) {
            debug_log( "Vehicle update failed! (" . $e->getMessage() . ")", $e->getTrace() );
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
            $vehicle = Vehicle::find( $id );
            $data = $this->service->destroy( $id );
            $logMessage = auth()->user()->username . ' has DELETED a VEHICLE with VEHICLE_ID: ' . $vehicle->id . ', vin: ' . $vehicle->vin . ' and Lot Number: ' . $vehicle->lot_number;
            store_activity( $logMessage, $vehicle->toArray(), $id, ActivityType::DELETE );

            debug_log( "Vehicle deleted successfully!", $data );

            return api( $data )->success( 'Vehicle Deleted Successfully!' );
        } catch ( Exception $e ) {
            debug_log( "Vehicle deletion failed!", $e->getTrace() );

            return api()->fails( $e->getMessage(), Response::HTTP_BAD_REQUEST );
        }
    }

    public function vehicleImageAdd( $id, Request $request )
    {
        try {
            foreach ( $request->fileUrls as $url ) {
                if ( filter_var( $url, FILTER_VALIDATE_URL ) ) {
                    $uri = $uri = str_replace( env( 'AWS_S3_BASE_URL' ), '', $url );
                    $thumbnailFileName = str_replace( basename( $uri ), 'thumb-' . basename( $uri ), $uri );
                    $thumbnail = Storage::exists( $thumbnailFileName ) ? $thumbnailFileName : $uri;

                    $img = new VehicleImage();
                    $img->name = $uri;
                    $img->thumbnail = $thumbnail;
                    $img->vehicle_id = $id;
                    $img->type = $request->get( 'type', VehiclePhotoType::VEHICLE_PHOTO );
                    $img->save();
                }
            }

            debug_log( "Vehicle image upload successfully!" );

            return response()->json( [ 'responseCode' => 1, 'data' => 'Successfully upload images' ] );
        } catch ( Exception $e ) {
            return response()->json( [ 'responseCode' => 0, 'data' => $e->getMessage() ], 400 );
        }
    }

    public function fileUpload( Request $request )
    {
        $imageName = time() . '.' . $request->file->getClientOriginalExtension();
        $request->file->move( public_path( 'images' ), $imageName );

        return response()->json( [ 'success' => 'You have successfully upload file.' ] );
    }

    public function vehicleCheckBoxItem(): \Illuminate\Http\JsonResponse
    {
        $list = Feature::all( [ 'id', 'name' ] )->toArray();

        return response()->json( $list );
    }

    public function vehicleConditionItem(): \Illuminate\Http\JsonResponse
    {
        $list = Condition::all( [ 'id', 'name' ] )->toArray();

        return response()->json( $list );
    }

    public function vehicleSearch( Request $request ): \Illuminate\Http\JsonResponse
    {
        $data = $this->service->vehicleSearch( $request->all() );

        return response()->json( $data );
    }

    public function getVehicleColors( Request $request ): \Illuminate\Http\JsonResponse
    {
        $data = $this->service->getVehicleColors( $request->all() )->toArray();

        return response()->json( $data );
    }

    public function getVehicleWeight( Request $request )
    {
        $weight = app( VehicleWeightService::class )->vehicleWeight( $request->all() );

        return response()->json( [ 'weight' => $weight ] );
    }

    public function changeNoteStatus( $id, Request $request )
    {
        Vehicle::find( $id )->update( [ 'notes_status' => $request->get( 'note_status' ) ] );

        return response()->json( [ 'message' => $request->get( 'note_status' ) == '1' ? 'Note Closed successfully.' : 'Note opened successfully.' ] );
    }

    public function downloadPhotos( $id, Request $request )
    {
        try {
            $type = $request->get( 'type', VehiclePhotoType::VEHICLE_PHOTO );

            $allImages = VehicleImage::where( [ 'vehicle_id' => $id, 'type' => $type ] )->get();
            $v = $this->service->getById( $id )->toArray();

            if ( $allImages->count() == 0 ) {
                return 'No Images Found';
            }

            $file = $v[ 'vin' ] . '-' . trans( 'vehicle.photos_types.' . $type ) . '.zip';
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

            return 'Something went wrong.';
        } catch ( \Exception $e ) {
            throw new \Exception( 'Something went wrong.' );
        }
    }

    public function uploadvehicleImage( $id, Request $request )
    {
        $this->validate( $request, [
            'file' => 'required|mimes:jpeg,jpg,png,webp',
        ] );

        $upload = app( FileManager::class )->uploadImageWithThumbnail( $request->file, 'uploads/vehicles/images/' . $id );
        if ( !$upload ) {
            return response()->json( [ 'success' => 'false', 'data' => 'Failed to file upload' ], Response::HTTP_UNPROCESSABLE_ENTITY );
        }

        return response()->json( [ 'success' => 'true', 'data' => $upload ] );
    }

    public function uploadvehicleDocument( $id, Request $request )
    {
        $this->validate( $request, [
            'file' => 'required|mimes:jpeg,jpg,png,pdf',
        ] );

        $upload = app( FileManager::class )->upload( $request->file, 'uploads/vehicles/documents/' . $id );
        if ( !$upload ) {
            return response()->json( [ 'success' => 'false', 'data' => 'Failed to file upload' ], Response::HTTP_UNPROCESSABLE_ENTITY );
        }

        return response()->json( [ 'success' => 'true', 'data' => $upload ] );
    }

    public function bar( Request $request )
    {
        $text = ( isset( $_GET[ 'text' ] ) ? $_GET[ 'text' ] : '0' );
        $size = ( isset( $_GET[ 'size' ] ) ? $_GET[ 'size' ] : '20' );
        $width_scale = ( isset( $_GET[ 'width_scale' ] ) ? $_GET[ 'width_scale' ] : 1.0 );

        $orientation = ( isset( $_GET[ 'orientation' ] ) ? $_GET[ 'orientation' ] : 'horizontal' );
        $code_type = ( isset( $_GET[ 'codetype' ] ) ? $_GET[ 'codetype' ] : 'code128' );
        $code_string = '';

        if ( strtolower( $code_type ) == 'code128' ) {
            $chksum = 104;
            // Must not change order of array elements as the checksum depends on the array's key to validate final code
            $code_array = [
                ' ' => '212222', '!' => '222122', '"' => '222221', '#' => '121223', '$' => '121322', '%' => '131222', '&' => '122213', "'" => '122312', '(' => '132212', ')' => '221213', '*' => '221312', '+' => '231212', ',' => '112232', '-' => '122132', '.' => '122231', '/' => '113222', '0' => '123122', '1' => '123221', '2' => '223211', '3' => '221132', '4' => '221231', '5' => '213212', '6' => '223112', '7' => '312131', '8' => '311222', '9' => '321122', ':' => '321221', ';' => '312212', '<' => '322112', '=' => '322211', '>' => '212123', '?' => '212321', '@' => '232121', 'A' => '111323', 'B' => '131123', 'C' => '131321', 'D' => '112313', 'E' => '132113', 'F' => '132311', 'G' => '211313', 'H' => '231113', 'I' => '231311', 'J' => '112133', 'K' => '112331', 'L' => '132131', 'M' => '113123', 'N' => '113321', 'O' => '133121', 'P' => '313121', 'Q' => '211331', 'R' => '231131', 'S' => '213113', 'T' => '213311', 'U' => '213131', 'V' => '311123', 'W' => '311321', 'X' => '331121', 'Y' => '312113', 'Z' => '312311', '[' => '332111', '\\' => '314111', ']' => '221411', '^' => '431111', '_' => '111224', "\`" => '111422', 'a' => '121124', 'b' => '121421', 'c' => '141122', 'd' => '141221', 'e' => '112214', 'f' => '112412', 'g' => '122114', 'h' => '122411', 'i' => '142112', 'j' => '142211', 'k' => '241211', 'l' => '221114', 'm' => '413111', 'n' => '241112', 'o' => '134111', 'p' => '111242', 'q' => '121142', 'r' => '121241', 's' => '114212', 't' => '124112', 'u' => '124211', 'v' => '411212', 'w' => '421112', 'x' => '421211', 'y' => '212141', 'z' => '214121', '{' => '412121', '|' => '111143', '}' => '111341', '~' => '131141', 'DEL' => '114113', 'FNC 3' => '114311', 'FNC 2' => '411113', 'SHIFT' => '411311', 'CODE C' => '113141', 'FNC 4' => '114131', 'CODE A' => '311141', 'FNC 1' => '411131', 'Start A' => '211412', 'Start B' => '211214', 'Start C' => '211232', 'Stop' => '2331112',
            ];
            $code_keys = array_keys( $code_array );
            $code_values = array_flip( $code_keys );
            for ( $X = 1; $X <= strlen( $text ); ++$X ) {
                $activeKey = substr( $text, ( $X - 1 ), 1 );
                $code_string .= $code_array[ $activeKey ];
                $chksum = ( $chksum + ( $code_values[ $activeKey ] * $X ) );
            }
            $code_string .= $code_array[ $code_keys[ ( $chksum - ( intval( $chksum / 103 ) * 103 ) ) ] ];

            $code_string = '211214' . $code_string . '2331112';
        } elseif ( strtolower( $code_type ) == 'code39' ) {
            $code_array = [ '0' => '111221211', '1' => '211211112', '2' => '112211112', '3' => '212211111', '4' => '111221112', '5' => '211221111', '6' => '112221111', '7' => '111211212', '8' => '211211211', '9' => '112211211', 'A' => '211112112', 'B' => '112112112', 'C' => '212112111', 'D' => '111122112', 'E' => '211122111', 'F' => '112122111', 'G' => '111112212', 'H' => '211112211', 'I' => '112112211', 'J' => '111122211', 'K' => '211111122', 'L' => '112111122', 'M' => '212111121', 'N' => '111121122', 'O' => '211121121', 'P' => '112121121', 'Q' => '111111222', 'R' => '211111221', 'S' => '112111221', 'T' => '111121221', 'U' => '221111112', 'V' => '122111112', 'W' => '222111111', 'X' => '121121112', 'Y' => '221121111', 'Z' => '122121111', '-' => '121111212', '.' => '221111211', ' ' => '122111211', '$' => '121212111', '/' => '121211121', '+' => '121112121', '%' => '111212121', '*' => '121121211' ];

            // Convert to uppercase
            $upper_text = strtoupper( $text );

            for ( $X = 1; $X <= strlen( $upper_text ); ++$X ) {
                $code_string .= $code_array[ substr( $upper_text, ( $X - 1 ), 1 ) ] . '1';
            }

            $code_string = '1211212111' . $code_string . '121121211';
        } elseif ( strtolower( $code_type ) == 'code25' ) {
            $code_array1 = [ '1', '2', '3', '4', '5', '6', '7', '8', '9', '0' ];
            $code_array2 = [ '3-1-1-1-3', '1-3-1-1-3', '3-3-1-1-1', '1-1-3-1-3', '3-1-3-1-1', '1-3-3-1-1', '1-1-1-3-3', '3-1-1-3-1', '1-3-1-3-1', '1-1-3-3-1' ];

            for ( $X = 1; $X <= strlen( $text ); ++$X ) {
                for ( $Y = 0; $Y < count( $code_array1 ); ++$Y ) {
                    if ( substr( $text, ( $X - 1 ), 1 ) == $code_array1[ $Y ] ) {
                        $temp[ $X ] = $code_array2[ $Y ];
                    }
                }
            }

            for ( $X = 1; $X <= strlen( $text ); $X += 2 ) {
                $temp1 = explode( '-', $temp[ $X ] );
                $temp2 = explode( '-', $temp[ ( $X + 1 ) ] );
                for ( $Y = 0; $Y < count( $temp1 ); ++$Y ) {
                    $code_string .= $temp1[ $Y ] . $temp2[ $Y ];
                }
            }

            $code_string = '1111' . $code_string . '311';
        } elseif ( strtolower( $code_type ) == 'codabar' ) {
            $code_array1 = [ '1', '2', '3', '4', '5', '6', '7', '8', '9', '0', '-', '$', ':', '/', '.', '+', 'A', 'B', 'C', 'D' ];
            $code_array2 = [ '1111221', '1112112', '2211111', '1121121', '2111121', '1211112', '1211211', '1221111', '2112111', '1111122', '1112211', '1122111', '2111212', '2121112', '2121211', '1121212', '1122121', '1212112', '1112122', '1112221' ];

            // Convert to uppercase
            $upper_text = strtoupper( $text );

            for ( $X = 1; $X <= strlen( $upper_text ); ++$X ) {
                for ( $Y = 0; $Y < count( $code_array1 ); ++$Y ) {
                    if ( substr( $upper_text, ( $X - 1 ), 1 ) == $code_array1[ $Y ] ) {
                        $code_string .= $code_array2[ $Y ] . '1';
                    }
                }
            }
            $code_string = '11221211' . $code_string . '1122121';
        }

        // Pad the edges of the barcode
        $code_length = 10;
        for ( $i = 1; $i <= strlen( $code_string ); ++$i ) {
            $code_length = $code_length + (int) ( substr( $code_string, ( $i - 1 ), 1 ) );
        }

        if ( strtolower( $orientation ) == 'horizontal' ) {
            $img_width = $code_length * $width_scale;
            $img_height = $size;
        } else {
            $img_width = $size;
            $img_height = $code_length * $width_scale;
        }

        $image = \imagecreate( $img_width, $img_height );
        $black = \imagecolorallocate( $image, 0, 0, 0 );
        $white = \imagecolorallocate( $image, 255, 255, 255 );

        imagefill( $image, 0, 0, $white );

        $location = 5;
        for ( $position = 1; $position <= strlen( $code_string ); ++$position ) {
            $cur_size = $location + ( substr( $code_string, ( $position - 1 ), 1 ) );
            if ( strtolower( $orientation ) == 'horizontal' ) {
                imagefilledrectangle( $image, $location * $width_scale, 0, $cur_size * $width_scale, $img_height, ( $position % 2 == 0 ? $white : $black ) );
            } else {
                imagefilledrectangle( $image, 0, $location * $width_scale, $img_width, $cur_size * $width_scale, ( $position % 2 == 0 ? $white : $black ) );
            }
            $location = $cur_size;
        }
        // Draw barcode to the screen
        header( 'Content-type: image/png' );
        imagepng( $image );
        imagedestroy( $image );
    }

    public function locationWiseTitleCount( Request $request )
    {
        $filters = [ 'status' => 1, 'limit' => -1 ];
        if ( in_array( optional( auth()->user() )->role, [ Roles::LOCATION_ADMIN, Roles::LOCATION_VIEW_ADMIN ] ) ) {
            $filters[ 'include_ids' ] = auth()->user()->locations;
        }
        $activeLocations = app( LocationService::class )->all( $filters );
        $response[] = [ 'location' => 'All', 'title_counts' => $this->service->locationWiseVehicleTitleCount() ];
        $activeLocations->map( function ( $item ) use ( &$response ) {
            $response[] = [ 'location' => $item->name, 'title_counts' => $this->service->locationWiseVehicleTitleCount( $item->id ) ];
        } );

        return response()->json( $response );
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function notes( Request $request ): \Illuminate\Http\JsonResponse
    {
        $data = app( NoteService::class )->vehicleNotes( $request->all() )->toArray();

        $data = ( new PaginatorPresenter( $data ) )->presentBy( NotePresenter::class );

        $data[ 'yard_show' ] = $this->service->isShowYard();
        return response()->json( $data );
    }

    public function conditionReportModal( $id, Request $request )
    {
        $model = $this->service->getById( $id );
        $features = Feature::all();

        return view( 'modals.condition_report_modal', compact( 'model', 'features' ) );
    }

    public function conditionReportPdf( $id, Request $request )
    {
        ini_set( 'memory_limit', '2000M' );
        set_time_limit( 0 );
        $vehicle = $this->service->getById( $id );
        if ( empty( $vehicle ) ) {
            throw new \Exception( 'Vehicle not found' );
        }
        $features = Feature::all();
        $vehicleFeatureIds = $vehicle->vehicle_features->where( 'value', 1 )->pluck( 'features_id' )->toArray();
        $vehicleConditions = $vehicle->vehicle_conditions->pluck( 'value', 'condition_id' );
        $pdf = PDF::loadView( 'pdf.condition_report', compact( [ 'vehicle', 'features', 'vehicleFeatureIds', 'vehicleConditions' ] ) );

        return $pdf->stream( 'CONDITION_REPORT2_FOR_VIN_' . data_get( $vehicle, 'vin' ) . '.pdf' );
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportExcel( Request $request )
    {
        if(env("APP_ENV_TYPE") != "PRODUCTION") {
            ini_set( 'memory_limit', '2000M' );
        }else {
            ini_set( 'memory_limit', '4000M' );
        }

        set_time_limit( 0 );
        if ( $request->auth_user_id ) {
            force_login( $request->auth_user_id );
        }

        return Excel::download( new VehiclesExport( $request->all() ), 'vehicles.xlsx' );
    }

    public function vehicleFeatures()
    {
        $data = Feature::select( 'id', 'name' )->get();

        return response()->json( $data );
    }

    /**
     * Set Vehicle handed over date
     *
     * @param $id
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function updateHandedOverDate( $id, Request $request ): \Illuminate\Http\JsonResponse
    {
        $this->validate( $request, [ 'handed_over_date' => 'required' ] );

        try {
            $data = Vehicle::find( $id )->update( $request->only( [ 'handed_over_date' ] ) );

            debug_log( "Vehicle updated successfully!" );

            return api( $data )->success( 'Vehicle Updated successfully!' );
        } catch ( \Exception $e ) {
            debug_log( "Vehicle update failed! (" . $e->getMessage() . ")", $e->getTrace() );

            return api()->fails( $e->getMessage() );
        }
    }

    public function vehicleDropdown( Request $request )
    {
        $query = Vehicle::select( 'id', DB::raw( 'vin AS title' ) );

        if ( optional( auth()->user() )->role == Roles::CUSTOMER ) {
            $query->where( 'customer_user_id', auth()->user()->id );
        }

        return response()->json( array_merge( [ [ 'id' => '', 'title' => 'Select One' ] ], $query->orderBy( 'id', 'DESC' )->limit( 500 )->get()->toArray() ) );
    }

    /**
     * @param $id
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadDocuments( $id, Request $request ): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        try {
            $type = $request->get( 'type', VehicleDocumentType::DOCUMENT );
            $vehicle = Vehicle::findOrFail( $id );
            $allDocuments = VehicleDocument::where( [ 'vehicle_id' => $id ] )->where( 'doc_type', $type )->get();

            if ( $allDocuments->count() == 0 ) {
                throw new \Exception( 'No Documents Found' );
            }

            $file = $vehicle->vin . ( $type == VehicleDocumentType::DOCUMENT ? '_documents' : '_invoices' ) . '.zip';
            $zipFileUrl = public_path( 'uploads/' . $file );

            if ( file_exists( $zipFileUrl ) ) {
                unlink( $zipFileUrl );
            }

            $zip = new Filesystem( new ZipArchiveAdapter( $zipFileUrl ) );

            foreach ( $allDocuments as $files ) {
                $zip->put( basename( $files->name ), file_get_contents( Storage::url( $files->name ) ) );
            }
            $zip->getAdapter()->getArchive()->close();

            if ( file_exists( $zipFileUrl ) ) {
                return response()->download( $zipFileUrl )->deleteFileAfterSend( true );
            }

            throw new \Exception( 'Something went wrong.' );
        } catch ( \Exception $e ) {
            throw new \Exception( $e->getMessage() );
        }
    }

    public function trackingVehicleInfo( Request $request )
    {
        $vehicle = null;
        if ( !empty( $request->search ) ) {
            $vehicle = $this->service->trackingVehicle( $request->search );
        }

        if ( $vehicle ) {
            return new VehicleTrackingResource( $vehicle );
        }

        return response()->json( [ 'message' => 'No Data Found' ] );
    }
}
