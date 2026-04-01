<?php


namespace App\Services\Vehicle;


use App\Enums\Roles;
use App\Enums\VehicleDocumentType;
use App\Enums\VehiclePhotoType;
use App\Enums\VehicleStatus;
use App\Enums\VisibilityStatus;
use App\Models\TowingRequest;
use App\Models\Vehicle;
use App\Models\VehicleColor;
use App\Models\VehicleCondition;
use App\Models\VehicleDocument;
use App\Models\VehicleFeature;
use App\Models\VehicleImage;
use App\Services\BaseService;
use App\Services\Storage\FileManager;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class VehicleService extends BaseService
{
    public function all( array $filters = [] )
    {
        $query = Vehicle::query()->with( [ 'towing_request', 'location', 'yard', 'customer', 'export', 'vehicle_claims', 'vehicle_documents', 'invoice_photos', 'vehicle_image' ] )->withCount( [
            'notes',
            'vehicle_image',
            'pickup_photos',
        ] );

        if ( !empty( $filters[ 'vcr' ] ) ) {
            $query->where( DB::raw( 'LOWER(vcr)' ), 'LIKE', '%' . strtolower( trim( $filters[ 'vcr' ] ) ) . '%' );
        }

        if ( !empty( $filters[ 'hat_number' ] ) ) {
            $query->where( 'hat_number', $filters[ 'hat_number' ] );
        }

        if ( !empty( $filters[ 'export_id' ] ) ) {
            $query->where( 'export_id', $filters[ 'export_id' ] );
        }

        if ( !empty( $filters[ 'towing_request_date' ] ) ) {
            $query->whereHas( 'towing_request', function ( $q ) use ( $filters ) {
                $q->where( DB::raw( 'LOWER(towing_request_date)' ), 'LIKE', '%' . strtolower( trim( $filters[ 'towing_request_date' ] ) ) . '%' );
            } );
        }

        if ( !empty( $filters[ 'deliver_date' ] ) ) {
            $query->whereHas( 'towing_request', function ( $q ) use ( $filters ) {
                $q->where( DB::raw( 'LOWER(deliver_date)' ), 'LIKE', '%' . strtolower( trim( $filters[ 'deliver_date' ] ) ) . '%' );
            } );
        }

        if ( !empty( $filters[ 'title_received_date' ] ) ) {
            $query->whereHas( 'towing_request', function ( $q ) use ( $filters ) {
                $q->where( DB::raw( 'LOWER(title_received_date)' ), 'LIKE', '%' . strtolower( trim( $filters[ 'title_received_date' ] ) ) . '%' );
            } );
        }

        if ( !empty( $filters[ 'eta' ] ) ) {
            $query->whereHas( 'export', function ( $q ) use ( $filters ) {
                $q->where( DB::raw( 'LOWER(eta)' ), 'LIKE', '%' . strtolower( trim( $filters[ 'eta' ] ) ) . '%' );
            } );
        }

        if ( !empty( $filters[ 'year' ] ) ) {
            $query->where( DB::raw( 'LOWER(year)' ), 'LIKE', '%' . strtolower( trim( $filters[ 'year' ] ) ) . '%' );
        }

        if ( !empty( $filters[ 'make' ] ) ) {
            $query->where( DB::raw( 'LOWER(make)' ), 'LIKE', '%' . strtolower( trim( $filters[ 'make' ] ) ) . '%' );
        }

        if ( !empty( $filters[ 'model' ] ) ) {
            $query->where( DB::raw( 'LOWER(model)' ), 'LIKE', '%' . strtolower( trim( $filters[ 'model' ] ) ) . '%' );
        }

        if ( !empty( $filters[ 'color' ] ) ) {
            $query->where( DB::raw( 'LOWER(color)' ), 'LIKE', '%' . strtolower( trim( $filters[ 'color' ] ) ) . '%' );
        }

        if ( !empty( $filters[ 'vin' ] ) ) {
            $query->where( DB::raw( 'LOWER(vin)' ), 'LIKE', '%' . strtolower( trim( $filters[ 'vin' ] ) ) . '%' );
        }

        if ( !empty( $filters[ 'lot_no' ] ) ) {
            $query->where( DB::raw( 'LOWER(lot_number)' ), 'LIKE', '%' . strtolower( trim( $filters[ 'lot_no' ] ) ) . '%' );
        }

        if ( !empty( $filters[ 'buyer_id' ] ) ) {
            $query->where( DB::raw( 'LOWER(license_number)' ), 'LIKE', '%' . strtolower( trim( $filters[ 'buyer_id' ] ) ) . '%' );
        }

        if ( isset( $filters[ 'keys' ] ) ) {
            $query->where( 'keys', $filters[ 'keys' ] );
        }

        if ( isset( $filters[ 'vehicle_type' ] ) ) {
            $query->where( 'vehicle_type', $filters[ 'vehicle_type' ] );

        }

        if ( isset( $filters[ 'title' ] ) ) {
            $query->whereHas( 'towing_request', function ( $q ) use ( $filters ) {
                $q->where( 'title_type', $filters[ 'title' ] );
            } );
        }

        if ( isset( $filters[ 'notes_status' ] ) ) {
            $query->where( 'notes_status', '=', $filters[ 'notes_status' ] );
            /*if ( $filters['notes_status'] == NoteStatus::OPEN ) {
            } else {
                $query->whereHas('notes');
                $query->where( 'notes_status', '=', NoteStatus::CLOSED );
            }*/
        }

        if ( !empty( $filters[ 'location' ] ) ) {
            $query->whereHas( 'location', function ( $q ) use ( $filters ) {
                $q->where( 'id', $filters[ 'location' ] );
            } );
        }

        if ( in_array( optional( auth()->user() )->role, [ Roles::LOCATION_ADMIN, Roles::LOCATION_VIEW_ADMIN, Roles::LOCATION_RESTRICTED_ADMIN ] ) ) {
            $query->whereIn( 'location_id', auth()->user()->locations );
            if ( optional( auth()->user() )->role === Roles::LOCATION_RESTRICTED_ADMIN ) {
                $query->whereIn( 'status', [ VehicleStatus::ON_HAND, VehicleStatus::ON_THE_WAY ] );
            }
        }

        if ( !empty( $filters[ 'status' ] ) ) {
            if ( !is_array( $filters[ 'status' ] ) ) {
                $filters[ 'status' ] = explode( ',', $filters[ 'status' ] );
            }
            $query->whereIn( 'status', $filters[ 'status' ] );
        }

        if ( !empty( $filters[ 'excluded_status' ] ) ) {
            if ( !is_array( $filters[ 'excluded_status' ] ) ) {
                $filters[ 'excluded_status' ] = [ $filters[ 'excluded_status' ] ];
            }
            $query->whereNotIn( 'status', $filters[ 'excluded_status' ] );
        }

        if ( isset( $filters[ 'damage_claim' ] ) ) {
            if ( $filters[ 'damage_claim' ] ) {
                $query->whereHas( 'vehicle_claims' );
            } else {
                $query->whereDoesntHave( 'vehicle_claims' );
            }
        }

        if ( isset( $filters[ 'claim_status' ] ) ) {
            $query->whereHas( 'vehicle_claims', function ( $q ) use ( $filters ) {
                $q->where( 'claim_status', $filters[ 'claim_status' ] );
            } );
        }

        if ( !empty( $filters[ 'container_no' ] ) ) {
            $query->whereHas( 'export', function ( $q ) use ( $filters ) {
                $q->where( DB::raw( 'LOWER(container_number)' ), 'LIKE', '%' . strtolower( trim( $filters[ 'container_no' ] ) ) . '%' );
            } );
        }

        if ( isset( $filters[ 'customer_name' ] ) ) {
            $query->where( 'customer_user_id', $filters[ 'customer_name' ] );
        }

        if ( !empty( $filters[ 'yard_id' ] ) ) {
            $query->where( 'yard_id', $filters[ 'yard_id' ] );
        }

        if ( !empty( $filters[ 'hybrid' ] ) ) {
            $query->where( 'hybrid', $filters[ 'hybrid' ] );
        }

        if ( optional( auth()->user() )->role == Roles::CUSTOMER ) {
            $query->where( 'customer_user_id', auth()->user()->id );
        } elseif ( !empty( $filters[ 'customer_user_id' ] ) ) {
            $query->where( 'customer_user_id', $filters[ 'customer_user_id' ] );
        }

        if ( optional( auth()->user() )->customers ) {
            $query->whereHas( 'customer', function ( $q ) {
                $q->whereIn( 'legacy_customer_id', auth()->user()->customers );
            } );
        }

        if ( !empty( $filters[ 'loading_type' ] ) ) {
            $query->where( 'load_status', $filters[ 'loading_type' ] );
        }

        if ( !empty( $filters[ 'vehicle_photos' ] ) && $filters[ 'vehicle_photos' ] === 'yes' ) {
            $query->whereHas( 'vehicle_image');
        } else if ( !empty( $filters[ 'vehicle_photos' ] ) && $filters[ 'vehicle_photos' ] === 'no' ) {
            $query->whereDoesntHave( 'vehicle_image');
        }

        if ( !empty( $filters[ 'vehicle_global_search' ] ) ) {
            $filters[ 'vehicle_global_search' ] = trim( $filters[ 'vehicle_global_search' ] );
            $query->where( function ( $q ) use ( $filters ) {
                $q->orWhere( DB::raw( 'LOWER(make)' ), 'LIKE', '%' . strtolower( $filters[ 'vehicle_global_search' ] ) . '%' )
                    ->orWhere( DB::raw( 'LOWER(model)' ), 'LIKE', '%' . strtolower( $filters[ 'vehicle_global_search' ] ) . '%' )
                    ->orWhere( DB::raw( 'LOWER(color)' ), 'LIKE', '%' . strtolower( $filters[ 'vehicle_global_search' ] ) . '%' )
                    ->orWhere( DB::raw( 'LOWER(vin)' ), 'LIKE', '%' . strtolower( $filters[ 'vehicle_global_search' ] ) . '%' )
                    ->orWhere( DB::raw( 'LOWER(lot_number)' ), 'LIKE', '%' . strtolower( $filters[ 'vehicle_global_search' ] ) . '%' )
                    ->orWhereHas( 'customer', function ( $q ) use ( $filters ) {
                        $q->where( DB::raw( 'LOWER(customer_name)' ), 'LIKE', '%' . strtolower( $filters[ 'vehicle_global_search' ] ) . '%' );
                    } )->orWhereHas( 'export', function ( $q ) use ( $filters ) {
                        $q->where( DB::raw( 'LOWER(container_number)' ), 'LIKE', '%' . strtolower( $filters[ 'vehicle_global_search' ] ) . '%' )
                            ->orWhere( DB::raw( 'LOWER(ar_number)' ), 'LIKE', '%' . strtolower( $filters[ 'vehicle_global_search' ] ) . '%' );
                    } );
            } );
        }

        // For China Location Allow Master Admin
        if(auth()->user()->role == 2) {
            if( ! in_array(16, optional(auth()->user())->locations ?? [] ) ) {
                $query->where('location_id', '!=', 16);
            }
        }else if(  ! in_array(auth()->user()->role,  explode(",", env('CHINA_SHOW_ROLES') )  ) ) {
            $query->where('location_id', '!=', 16);
        }

        $orderByCol = Arr::get( $filters, 'order_by_column', 'id' );

        $query->orderBy( $orderByCol, Arr::get( $filters, 'order_by', 'desc' ) );
        $limit = Arr::get( $filters, 'limit', 20 );

        if ( $limit != -1 ) {
            return $query->paginate( $limit );
        }

        return Arr::get( $filters, 'query_only', false ) ? $query : $query->get();
    }

    public function getById( $id )
    {
        $query = Vehicle::with( [ 'towing_request', 'vehicle_image', 'export', 'auction_photos', 'pickup_photos', 'arrived_photos', 'customer', 'location', 'vehicle_conditions', 'vehicle_features', 'vehicle_documents', 'invoice_photos', 'notes', 'yard' ] );

        if ( optional( auth()->user() )->role == Roles::CUSTOMER ) {
            $query->where( 'customer_user_id', auth()->user()->id );
        }

        return $query->findOrFail( $id );
    }

    public function store( array $data )
    {
        return $this->saveVehicle( $data );
    }

    public function update( $id, array $data )
    {
        $this->removeVehiclePhotos( $id, Arr::get( $data, 'fileUrls.photos' ) );
        $this->removeVehiclePhotos( $id, Arr::get( $data, 'fileUrls.auction_photos' ), VehiclePhotoType::AUCTION_PHOTO );
        $this->removeVehiclePhotos( $id, Arr::get( $data, 'fileUrls.pickup_photos' ), VehiclePhotoType::PICKUP_PHOTO );
        $this->removeVehiclePhotos( $id, Arr::get( $data, 'fileUrls.arrived_photos' ), VehiclePhotoType::ARRIVE_PHOTO );
        $this->removeVehicleDocuments( $id, Arr::get( $data, 'fileUrls.vehicle_documents' ), VehicleDocumentType::DOCUMENT );
        $this->removeVehicleDocuments( $id, Arr::get( $data, 'fileUrls.invoice_photos' ), VehicleDocumentType::INVOICE );

        return $this->saveVehicle( $data, $id );
    }

    public function destroy( $id )
    {
        return Vehicle::find( $id )->delete();
    }

    private function saveVehicle( $data, $id = null )
    {
        unset( $data[ 'version_id' ] );

        if($data['status'] != VehicleStatus::PICKED_UP){
            unset( $data[ 'status' ] );
        }

        if ( isset( $data[ 'additional_charges' ] ) ) {
            $data[ 'additional_charges' ] = (float) $data[ 'additional_charges' ];
        }
        if ( isset( $data[ 'storage_amount' ] ) ) {
            $data[ 'storage_amount' ] = (float) $data[ 'storage_amount' ];
        }
        $vehicle = Vehicle::findOrNew( $id );

        $towingObj = TowingRequest::findOrNew( $vehicle->towing_request_id );
        if ( empty( $towingObj->deliver_date ) && !empty( $data[ 'deliver_date' ] )  && $vehicle->status == VehicleStatus::ON_THE_WAY) {
            $data[ 'status' ] = VehicleStatus::ON_HAND;
        }else if($id == null && !empty( $data[ 'deliver_date' ] )) {
            $data[ 'status' ] = VehicleStatus::ON_HAND;
        }else if(empty( $data[ 'deliver_date' ] ) && $id) {
            $data[ 'status' ] = VehicleStatus::ON_THE_WAY;
        }

        $towingObj->fill( $data );
        /*$towingObj->condition = isset( $data['condition'] ) ? $data['condition'] : null;
        $towingObj->damaged = isset( $data['damaged'] ) ? $data['damaged'] : null;
        $towingObj->pictures = isset( $data['pictures'] ) ? $data['pictures'] : null;
        $towingObj->towed = isset( $data['towed'] ) ? $data['towed'] : null;
        $towingObj->title_type = isset( $data['title_type'] ) ? $data['title_type'] : null;
        $towingObj->title_received_date = isset( $data['title_received_date'] ) ? $data['title_received_date'] : null;
        $towingObj->title_number = isset( $data['title_number'] ) ? $data['title_number'] : null;
        $towingObj->title_state = isset( $data['title_state'] ) ? $data['title_state'] : null;
        $towingObj->towing_request_date = isset( $data['towing_request_date'] ) ? $data['towing_request_date'] : null;
        $towingObj->pickup_date = isset( $data['pickup_date'] ) ? $data['pickup_date'] : null;
        $towingObj->deliver_date = isset( $data['deliver_date'] ) ? $data['deliver_date'] : null;
        */
        if ( isset( $data[ 'title' ] ) ) {
            $towingObj->title_received = $data[ 'title' ];
        }
        $towingObj->note = $data[ 'key_note' ] ?? '';
        $towingObj->save();

        $data[ 'towing_request_id' ] = $towingObj->id;

        $data = $this->weightCalculation($data);
        $vehicle->fill( $data );
        $vehicle->updated_at = date( 'Y-m-d H:i:s' );
        $vehicle->save();

        // saving vehicle photos, auction_photos, pickup_photos, arrived_photos
        $this->saveVehiclePhoto( Arr::get( $data, 'fileUrls.photos', [] ), $vehicle->id );
        $this->saveVehiclePhoto( Arr::get( $data, 'fileUrls.auction_photos', [] ), $vehicle->id, VehiclePhotoType::AUCTION_PHOTO );
        $this->saveVehiclePhoto( Arr::get( $data, 'fileUrls.pickup_photos', [] ), $vehicle->id, VehiclePhotoType::PICKUP_PHOTO );
        $this->saveVehiclePhoto( Arr::get( $data, 'fileUrls.arrived_photos', [] ), $vehicle->id, VehiclePhotoType::ARRIVE_PHOTO );
        $this->saveVehicleDocument( Arr::get( $data, 'fileUrls.document_files', [] ), $vehicle->id );
        $this->saveVehicleDocument( Arr::get( $data, 'fileUrls.invoice_photos', [] ), $vehicle->id, VehicleDocumentType::INVOICE );

        $featureIds = Arr::get( $data, 'vehicle_features', [] );
        VehicleFeature::where( 'vehicle_id', $vehicle->id )->delete();
        foreach ( $featureIds as $featureId ) {
            if ( $featureId ) {
                VehicleFeature::updateOrCreate(
                    [ 'vehicle_id' => $vehicle->id, 'features_id' => $featureId ],
                    [ 'value' => 1 ]
                );
            }
        }

        foreach ( Arr::get( $data, 'vehicle_conditions', [] ) as $key => $value ) {
            VehicleCondition::updateOrCreate(
                [ 'vehicle_id' => $vehicle->id, 'condition_id' => $key ],
                [ 'value' => $value ]
            );
        }

        return $vehicle;
    }

    public function weightCalculation($data)
    {
        if($data['weight']) {
             $data['weight_in_kg'] = round($data['weight'] * 0.453592);
        }else if($data['weight_in_kg']) {
            $data['weight'] = round( $data['weight_in_kg'] * 2.20462);;
        }

        return $data;
    }

    private function saveVehicleDocument( $documents, $vehicleId, $type = VehicleDocumentType::DOCUMENT )
    {
        foreach ( $documents as $url ) {
            if ( filter_var( $url, FILTER_VALIDATE_URL ) ) {
                $imageobj = new VehicleDocument();
                $imageobj->name = str_replace( env( 'AWS_S3_BASE_URL' ), '', $url );
                $imageobj->vehicle_id = $vehicleId;
                $imageobj->doc_type = $type;
                $imageobj->save();
            }
        }
    }

    private function saveVehiclePhoto( $photos, $vehicleId, $type = VehiclePhotoType::VEHICLE_PHOTO )
    {
        foreach ( $photos as $url ) {
            $uri = filter_var( $url, FILTER_VALIDATE_URL ) ? str_replace( env( 'AWS_S3_BASE_URL' ), '', $url ) : null;
            if ( $uri ) {
                $thumbnailFileName = str_replace( basename( $uri ), 'thumb-' . basename( $uri ), $uri );
                $thumbnail = Storage::exists( $thumbnailFileName ) ? $thumbnailFileName : $uri;
                $imageobj = new VehicleImage();
                $imageobj->name = $uri;
                $imageobj->thumbnail = $thumbnail;
                $imageobj->vehicle_id = $vehicleId;
                $imageobj->type = $type;
                $imageobj->save();
            }
        }
    }

    private function removeVehiclePhotos( $vehicleId, $newPhotos, $type = VehiclePhotoType::VEHICLE_PHOTO )
    {
        $imageIds = VehicleImage::where( [
            'vehicle_id' => $vehicleId,
            'type'       => $type,
        ] )->whereNotIn( 'id', collect( $newPhotos )->reject( function ( $item ) {
            return is_string( $item );
        } )->pluck( 'id' )->toArray() )
            ->pluck( 'id' )
            ->toArray();

        VehicleImage::whereIn( 'id', $imageIds )->delete();
    }

    private function removeVehicleDocuments( $vehicleId, $newDocs, $type = VehicleDocumentType::DOCUMENT )
    {
        $ids = VehicleDocument::where( [
            'vehicle_id' => $vehicleId,
            'doc_type'   => $type,
        ] )->whereNotIn( 'id', collect( $newDocs )->reject( function ( $item ) {
            return is_string( $item );
        } )->pluck( 'id' )->toArray() )
            ->pluck( 'id' )
            ->toArray();

        VehicleDocument::whereIn( 'id', $ids )->delete();
    }

    public function getByVin( $q )
    {
        return Vehicle::with( [ 'towing_request', 'vehicle_image', 'export', 'auction_photos', 'pickup_photos', 'arrived_photos', 'customer', 'location', 'vehicle_conditions', 'vehicle_features', 'vehicle_documents', 'invoice_photos', 'notes' ] )
            ->where( 'vin', '=', $q )
            ->orWhere( 'container_number', '=', $q )
            ->orWhere( 'lot_number', '=', $q )
            ->first();
    }

    public function vehicleSearch( $filters = [] )
    {
        $query = Vehicle::select( 'id', 'vin' )->where( 'status', VehicleStatus::ON_HAND )->whereNull( 'export_id' );

        if ( !empty( $filters[ 'vin' ] ) ) {
            $query->where( function ( $q ) use ( $filters ) {
                $q->where( DB::raw( 'LOWER(vin)' ), 'LIKE', '%' . strtolower( $filters[ 'vin' ] ) . '%' );
//                $q->orWhere( DB::raw( 'LOWER(lot_number)' ), 'LIKE', '%' . strtolower( $filters[ 'vin' ] ) . '%' );
            } );
        }

        if ( !empty( $filters[ 'exclude_ids' ] ) ) {
            $query->whereNotIn( 'id', $filters[ 'exclude_ids' ] );
        }

        // For China Location Allow Master Admin
        if(auth()->user()->role == 2) {
            if( ! in_array(16, optional(auth()->user())->locations ?? [] ) ) {
                $query->where('location_id', '!=', 16);
            }
        }else if(  ! in_array(auth()->user()->role,  explode(",", env('CHINA_SHOW_ROLES') )  ) ) {
            $query->where('location_id', '!=', 16);
        }

        $limit = Arr::get( $filters, 'limit', 20 );

        return $limit != '-1' ? $query->paginate( $limit ) : $query->get();
    }

    public function getWorthAmount( $filters = [] )
    {
        $query = Vehicle::query();

        if ( optional( auth()->user() )->role == Roles::CUSTOMER ) {
            $query->where( 'customer_user_id', auth()->user()->id );
        } elseif ( !empty( $filters[ 'customer_user_id' ] ) ) {
            $query->where( 'customer_user_id', $filters[ 'customer_user_id' ] );
        }

        return number_format( $query->sum( 'value' ), 2, '.', '' );
    }

    public function getVehicleColors( $filters = [] )
    {
        /*return Cache::rememberForever('vehicle_colors', function () {
            return Vehicle::select( [
                DB::raw( 'DISTINCT(color) AS color' ),
            ] )->where( 'color', '<>', '' )->get();
        });*/

        return VehicleColor::select( 'color' )->get();
    }

    public function uploadVehicleImage( $id, Request $request )
    {
        $upload = app( FileManager::class )->upload( $request->file, 'uploads/vehicles/images/' . $id );
        if ( !$upload ) {
            return response()->json( [ 'success' => 'false', 'data' => 'Failed to file upload' ], Response::HTTP_UNPROCESSABLE_ENTITY );
        }

        return response()->json( [ 'success' => 'true', 'data' => $upload ] );
    }

    public function uploadVehicleDocument( $id, Request $request )
    {
        $upload = app( FileManager::class )->upload( $request->file, 'uploads/vehicles/documents/' . $id );
        if ( !$upload ) {
            return response()->json( [ 'success' => 'false', 'data' => 'Failed to file upload' ], Response::HTTP_UNPROCESSABLE_ENTITY );
        }

        return response()->json( [ 'success' => 'true', 'data' => $upload ] );
    }

    public function locationWiseVehicleTitleCount( $location = null )
    {
        $query = Vehicle::query()
            ->join( 'towing_requests', 'vehicles.towing_request_id', '=', 'towing_requests.id' )
            ->join( 'locations', 'vehicles.location_id', '=', 'locations.id' )
            ->select( [
                DB::raw( 'IFNULL(SUM( IF(vehicles.status = 1, 1, 0) ), 0) AS on_hand' ),
                DB::raw( 'IFNULL(SUM( IF(vehicles.status = 1, 1, 0)  AND IF(towing_requests.title_type = 1, 1, 0)), 0) AS exportable' ),
                DB::raw( 'IFNULL(SUM( IF(vehicles.status = 1, 1, 0)  AND IF(towing_requests.title_type = 0, 1, 0)), 0) AS no_title' ),
                DB::raw( 'IFNULL(SUM( IF(vehicles.status = 3, 1, 0) ), 0) AS on_the_way' ),
                DB::raw( 'IFNULL(SUM( IF(vehicles.status = 1, 1, 0) AND IF(towing_requests.title_type = 2, 1, 0) ), 0) AS pending' ),
                DB::raw( 'IFNULL(SUM( IF(vehicles.status = 1, 1, 0) AND IF(towing_requests.title_type = 3, 1, 0)), 0) AS bos' ),
                DB::raw( 'IFNULL(SUM( IF(vehicles.status = 1, 1, 0) AND IF(towing_requests.title_type = 4, 1, 0)), 0) AS lien' ),
                DB::raw( 'IFNULL(SUM( IF(vehicles.status = 1, 1, 0) AND IF(towing_requests.title_type = 6, 1, 0)), 0) AS rejected' ),
                DB::raw( 'IFNULL(SUM( IF(vehicles.status = 1, 1, 0) AND IF(towing_requests.title_type = 5, 1, 0)), 0) AS mv907' ),
            ] )
            ->where( [ 'vehicles.status' => VisibilityStatus::ACTIVE, 'locations.status' => VisibilityStatus::ACTIVE ] );

        if ( optional( auth()->user() )->role == Roles::CUSTOMER ) {
            $query->where( 'vehicles.customer_user_id', auth()->user()->id );
        }

        if ( $location ) {
            $query->where( [ 'vehicles.location_id' => $location ] );
        } else if ( in_array( optional( auth()->user() )->role, [ Roles::LOCATION_ADMIN, Roles::LOCATION_VIEW_ADMIN ] ) ) {
            $query->whereIn( 'vehicles.location_id', auth()->user()->locations );
        }

        return $query->first();
    }

    public function trackingVehicle( $search )
    {
        $query = Vehicle::with( [ 'vehicle_image', 'export' ] )
            ->where( 'vin', $search )
            ->orWhere( 'lot_number', $search );

        return $query->first();
    }

    public function isShowYard()
    {
        return in_array(optional(auth()->user())->role, [Roles::MASTER_ADMIN, Roles::SUPER_ADMIN, Roles::LOCATION_ADMIN, Roles::EMPLOYEE, Roles::ACCOUNT, Roles::ADMIN]);
    }
}
