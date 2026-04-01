<?php


namespace App\Services\Export;


use App\Enums\ExportPhotoType;
use App\Enums\Roles;
use App\Enums\StreamshipLine;
use App\Enums\VehicleStatus;
use App\Models\DockReceipt;
use App\Models\Export;
use App\Models\ExportImage;
use App\Models\HoustanCustomCoverLetter;
use App\Models\Vehicle;
use App\Services\BaseService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ExportService extends BaseService
{
    /**
     * @param array $filters
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function all( array $filters = [] )
    {
        $query = Export::query()->with( [ 'customer', 'vehicles.customer', 'vehicles.location', 'vehicles.yard', 'houstan_custom_cover_letter.consignee_item', 'export_images', 'vehicles.vehicle_image', 'vehicles.invoice_photos', 'invoice' ] )
            ->withCount( [
            'vehicles', 'export_images' => function ( $q ) {
                $q->where( 'type', '=', ExportPhotoType::EXPORT_PHOTO );
            },
        ] );

        if ( in_array( optional( auth()->user() )->role, [ Roles::LOCATION_ADMIN, Roles::LOCATION_VIEW_ADMIN, Roles::LOCATION_RESTRICTED_ADMIN ] ) ) {
            $query->whereHas( 'vehicles', function ( $q ) {
                $q->whereIn( 'location_id', auth()->user()->locations );
                if ( optional( auth()->user() )->customers ) {
                    $q->whereHas( 'vehicles.customer', function ( $q ) {
                        $q->whereIn( 'legacy_customer_id', auth()->user()->customers );
                    } );
                }
            } );
        } elseif ( optional( auth()->user() )->role == Roles::CUSTOMER ) {
            $query->with( 'vehicles', function ( $q ) {
                $q->where( 'customer_user_id', auth()->user()->id );
            } );
            $query->whereHas( 'vehicles', function ( $q ) {
                $q->where( 'customer_user_id', auth()->user()->id );
            } );
        }

        if ( !empty( $filters[ 'location_id' ] ) ) {
            $query->whereHas( 'vehicles', function ( $q ) use ( $filters ) {
                $q->where( 'location_id', $filters[ 'location_id' ] );
            } );
        }

        if ( !empty( $filters[ 'hybrid_exist' ] )  &&  $filters[ 'hybrid_exist' ] == 1) {
            $query->whereHas( 'vehicles', function ( $q ) use ( $filters ) {
                $q->where( 'hybrid', $filters[ 'hybrid_exist' ]  );
            } );
        }

        if ( !empty( $filters[ 'hybrid_exist' ] )  &&  $filters[ 'hybrid_exist' ] == 2) {
            $query->whereDoesntHave('vehicles', function ($q) {
                $q->where('hybrid', 1);
            });
        }

        // For China Location Allow Master Admin
        if(auth()->user()->role == 2) {
            if( ! in_array(16, optional(auth()->user())->locations ?? [] ) ) {
//                $query->where('location_id', '!=', 16);
                $query->whereHas( 'vehicles', function ( $q ) use ( $filters ) {
                    $q->where( 'location_id', '!=', 16 );
                } );
            }
        }else if(  ! in_array(auth()->user()->role,  explode(",", env('CHINA_SHOW_ROLES') )  ) ) {
//            $query->where('location_id', '!=', 16);
            $query->whereHas( 'vehicles', function ( $q ) use ( $filters ) {
                $q->where( 'location_id', '!=', 16 );
            } );
        }

        if ( !empty( $filters[ 'select' ] ) ) {
            $query->select( $filters[ 'select' ] );
        }

        if ( !empty( $filters[ 'loading_of_vehicle' ] ) ) {
            $query->whereIn( 'status', [ VehicleStatus::MANIFEST, VehicleStatus::SHIPPED ] );
        }

        if ( !empty( $filters[ 'status' ] ) ) {
            $query->where( 'status', $filters[ 'status' ] );
        }

        if ( !empty( $filters[ 'customer_user_id' ] ) && (int) $filters[ 'customer_user_id' ] ) {
//            $query->where( 'customer_user_id', $filters[ 'customer_user_id' ] );
            $query->with( 'vehicles', function ( $q ) use ( $filters ) {
                $q->where( 'customer_user_id', $filters[ 'customer_user_id' ] );
            } );
            $query->whereHas( 'vehicles', function ( $q ) use ( $filters ) {
                $q->where( 'customer_user_id', $filters[ 'customer_user_id' ] );
            } );
        }

        if ( !empty( $filters[ 'consignee_id' ] ) && (int) $filters[ 'consignee_id' ] ) {
            $query->whereHas( 'houstan_custom_cover_letter', function ( $q ) use ( $filters ) {
                $q->where( 'consignee', $filters[ 'consignee_id' ] );
            } );
        }

        if ( !empty( $filters[ 'export_date' ] ) ) {
            $query->where( 'export_date', 'LIKE', '%' . trim( $filters[ 'export_date' ] ) . '%' );
        }

        if ( !empty( $filters[ 'loading_date' ] ) ) {
            $query->where( 'loading_date', 'LIKE', '%' . trim( $filters[ 'loading_date' ] ) . '%' );
        }

        if ( !empty( $filters[ 'container_type' ] ) ) {
            $query->where( 'container_type', $filters[ 'container_type' ] );
        }

        if ( !empty( $filters[ 'booking_number' ] ) ) {
            $query->where( DB::raw( 'LOWER(booking_number)' ), 'LIKE', '%' . strtolower( trim( $filters[ 'booking_number' ] ) ) . '%' );
        }

        if ( !empty( $filters[ 'destination' ] ) ) {
            $query->where( DB::raw( 'LOWER(destination)' ), 'LIKE', '%' . strtolower( trim( $filters[ 'destination' ] ) ) . '%' );
        }

        if ( !empty( $filters[ 'terminal' ] ) ) {
            $query->where( DB::raw( 'LOWER(terminal)' ), 'LIKE', '%' . strtolower( trim( $filters[ 'terminal' ] ) ) . '%' );
        }

        if ( !empty( $filters[ 'broker_name' ] ) ) {
            $query->where( DB::raw( 'LOWER(broker_name)' ), 'LIKE', '%' . strtolower( trim( $filters[ 'broker_name' ] ) ) . '%' );
        }

        if ( !empty( $filters[ 'eta' ] ) ) {
            $query->where( DB::raw( 'LOWER(eta)' ), 'LIKE', '%' . strtolower( trim( $filters[ 'eta' ] ) ) . '%' );
        }

        if ( !empty( $filters[ 'manifest_date' ] ) ) {
            $query->where( DB::raw( 'created_at' ), 'LIKE', '%' . trim( $filters[ 'manifest_date' ] ) . '%' );
        }

        if ( !empty( $filters[ 'ar_number' ] ) ) {
            $query->where( DB::raw( 'LOWER(ar_number)' ), 'LIKE', '%' . strtolower( trim( $filters[ 'ar_number' ] ) ) . '%' );
        }

        if ( !empty( $filters[ 'container_number' ] ) ) {
            $query->where( DB::raw( 'LOWER(container_number)' ), 'LIKE', '%' . strtolower( trim( $filters[ 'container_number' ] ) ) . '%' );
        }

        if ( !empty( $filters[ 'contact_details' ] ) ) {
            $query->where( DB::raw( 'LOWER(contact_details)' ), 'LIKE', '%' . strtolower( trim( $filters[ 'contact_details' ] ) ) . '%' );
        }

        if ( !empty( $filters[ 'port_of_loading' ] ) ) {
            $query->where( 'port_of_loading', $filters[ 'port_of_loading' ] );
        }

        if ( !empty( $filters[ 'port_of_discharge' ] ) ) {
            $query->where( 'port_of_discharge', $filters[ 'port_of_discharge' ] );
        }

        if ( !empty( $filters[ 'vessel' ] ) ) {
            $query->where( DB::raw( 'LOWER(vessel)' ), 'LIKE', '%' . strtolower( trim( $filters[ 'vessel' ] ) ) . '%' );
        }

        if ( !empty( $filters[ 'yard_id' ] )  ) {
            $query->whereHas( 'vehicles.yard', function ( $q ) use ( $filters ) {
                $q->where( 'id', $filters[ 'yard_id' ] );
            } );
        }

        if ( !empty( $filters[ 'export_global_search' ] ) ) {
            $filters[ 'export_global_search' ] = trim( $filters[ 'export_global_search' ] );
            $query->where( function ( $q ) use ( $filters ) {
                $q->where( DB::raw( 'LOWER(booking_number)' ), 'LIKE', '%' . strtolower( $filters[ 'export_global_search' ] ) . '%' )
                    ->orWhere( DB::raw( 'LOWER(broker_name)' ), 'LIKE', '%' . strtolower( $filters[ 'export_global_search' ] ) . '%' )
                    ->orWhere( DB::raw( 'LOWER(ar_number)' ), 'LIKE', '%' . strtolower( $filters[ 'export_global_search' ] ) . '%' )
                    ->orWhere( DB::raw( 'LOWER(container_number)' ), 'LIKE', '%' . strtolower( $filters[ 'export_global_search' ] ) . '%' )
                    ->orWhere( DB::raw( 'LOWER(contact_details)' ), 'LIKE', '%' . strtolower( $filters[ 'export_global_search' ] ) . '%' )
                    ->orWhere( DB::raw( 'LOWER(vessel)' ), 'LIKE', '%' . strtolower( $filters[ 'export_global_search' ] ) . '%' );
            } );
        }

        $query->orderBy( Arr::get( $filters, 'order_by_column', 'id' ), Arr::get( $filters, 'order_by', 'desc' ) );
        $limit = Arr::get( $filters, 'limit', 20 );

        if ( $limit != -1 ) {
            return $query->paginate( $limit );
        }

        return Arr::get( $filters, 'query_only', false ) ? $query : $query->get();
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function getById( $id )
    {
        $query = Export::with( [ 'customer.user', 'exporter', 'houstan_custom_cover_letter.consignee_item', 'houstan_custom_cover_letter.notify_party_item', 'houstan_custom_cover_letter.menifest_consignee_item', 'dock_receipt', 'export_images', 'empty_container_photos', 'loading_photos', 'loaded_photos', 'vehicles' ] );

        if ( optional( auth()->user() )->role == Roles::CUSTOMER ) {
            $query->whereHas( 'vehicles', function ( $q ) {
                $q->where( 'customer_user_id', auth()->user()->id );
            } );
        }

        return $query->findOrFail( $id );
    }

    /**
     * @param array $data
     *
     * @return mixed
     */
    public function store( array $data )
    {
        return $this->saveExport( $data );
    }

    /**
     * @param $id
     * @param array $data
     *
     * @return mixed
     */
    public function update( $id, array $data )
    {
        if(isset($data['fileurl'])) {
            $this->removeExportPhotos( $id, Arr::get( $data, 'fileurl.container_images' ) );
            $this->removeExportPhotos( $id, Arr::get( $data, 'fileurl.empty_container_photos' ), ExportPhotoType::EMPTY_CONTAINER_PHOTO );
            $this->removeExportPhotos( $id, Arr::get( $data, 'fileurl.loading_photos' ), ExportPhotoType::LOADING_PHOTO );
            $this->removeExportPhotos( $id, Arr::get( $data, 'fileurl.loaded_photos' ), ExportPhotoType::LOADED_PHOTO );
        }

        Vehicle::where( 'export_id', $id )
            ->whereNotIn( 'id', $data[ 'vehicle_ids' ] )
            ->update( [ 'export_id' => null, 'status' => VehicleStatus::ON_HAND ] );

        return $this->saveExport( $data, $id );
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function destroy( $id )
    {
        Vehicle::where( 'export_id', $id )->update( [ 'export_id' => null, 'status' => VehicleStatus::ON_HAND ] );

        return Export::find( $id )->delete();
    }

    /**
     * @param $data
     * @param null $id
     *
     * @return mixed
     */
    private function saveExport( $data, $id = null )
    {
        unset( $data[ 'version_id' ] );

        $url = Arr::get( $data, 'fileurl.export_invoice_photo.0' );
        if ( $url && filter_var( $url, FILTER_VALIDATE_URL ) ) {
            $data[ 'export_invoice' ] = str_replace( env( 'AWS_S3_BASE_URL' ), '', $url );
        }
        $export = Export::findOrNew( $id );

        if ( empty( $export->handed_over_date ) && !empty( $data[ 'handed_over_date' ] ) ) {
            Vehicle::whereIn( 'id', $data[ 'vehicle_ids' ] )->update( [
                'handed_over_date' => $data[ 'handed_over_date' ],
            ] );
        }

        $export->fill( $data );
        $export->save();

        $this->saveExportPhoto( Arr::get( $data, 'fileurl.container_images', [] ), $export->id );
        $this->saveExportPhoto( Arr::get( $data, 'fileurl.empty_container_photos', [] ), $export->id, ExportPhotoType::EMPTY_CONTAINER_PHOTO );
        $this->saveExportPhoto( Arr::get( $data, 'fileurl.loading_photos', [] ), $export->id, ExportPhotoType::LOADING_PHOTO );
        $this->saveExportPhoto( Arr::get( $data, 'fileurl.loaded_photos', [] ), $export->id, ExportPhotoType::LOADED_PHOTO );

        Vehicle::whereIn( 'id', $data[ 'vehicle_ids' ] )->update( [
            'export_id'        => $export->id,
            'status'           => $export->status,
            'container_number' => $export->container_number,
        ] );

        HoustanCustomCoverLetter::updateOrCreate(
            [ 'export_id' => $export->id ],
            Arr::only( $data, [
                'vehicle_location',
                'exporter_id',
                'exporter_type_issuer',
                'transportation_value',
                'exportation_value',
                'exporter_dob',
                'ultimate_consignee_dob',
                'consignee',
                'notify_party',
                'menifest_consignee',
            ] )
        );

        $data[ 'container_type' ] = Arr::get( $data, 'dock_container_type', '' );
        DockReceipt::updateOrCreate(
            [ 'export_id' => $export->id ],
            Arr::only( $data, [
                'awb_number',
                'export_reference',
                'forwarding_agent',
                'domestic_routing_instructions',
                'pre_carriage_by',
                'place_of_receipt_by_pre_carrier',
                'exporting_carrier',
                'final_destination',
                'loading_terminal',
                'container_type',
                'number_of_packages',
                'by',
                'date',
                'auto_receiving_date',
                'auto_cut_off',
                'vessel_cut_off',
                'sale_date',
            ] )
        );

        return $export;
    }

    private function saveExportPhoto( $photos, $export_id, $type = ExportPhotoType::EXPORT_PHOTO )
    {
        foreach ( $photos as $url ) {
            $uri = filter_var( $url, FILTER_VALIDATE_URL ) ? str_replace( env( 'AWS_S3_BASE_URL' ), '', $url ) : null;
            if ( $uri ) {
                $thumbnailFileName = str_replace( basename( $uri ), 'thumb-' . basename( $uri ), $uri );
                $thumbnail = Storage::exists( $thumbnailFileName ) ? $thumbnailFileName : $uri;
                $imageobj = new ExportImage();
                $imageobj->name = $uri;
                $imageobj->thumbnail = $thumbnail;
                $imageobj->export_id = $export_id;
                $imageobj->type = $type;
                $imageobj->save();
            }
        }
    }

    private function removeExportPhotos( $exportId, $newPhotos, $type = ExportPhotoType::EXPORT_PHOTO )
    {
        if(!is_array($newPhotos)) {
            return;
        }

        $imageIds = ExportImage::where( [
            'export_id' => $exportId,
            'type'      => $type,
        ] )->whereNotIn( 'id', collect( $newPhotos )->reject( function ( $item ) {
            return is_string( $item );
        } )->pluck( 'id' )->toArray() )
            ->pluck( 'id' )
            ->toArray();

        ExportImage::whereIn( 'id', $imageIds )->delete();
    }

    public function getContainerList( $filters = [] )
    {
        $containerColumn = 'container_number';
        if ( isset( $filters[ 'with_ar_number' ] ) ) {
            $containerColumn = DB::raw( "CONCAT(container_number, ' | ', ar_number) AS container_number" );
        }
        $query = Export::select( $containerColumn, 'id' )
            ->where( 'container_number', '<>', '' )
            ->whereNotNull( 'container_number' );

        if ( !empty( $filters[ 'q' ] ) ) {
            if ( Str::contains( $filters[ 'q' ], ' | ' ) ) {
                $filters[ 'q' ] = Arr::get( explode( ' | ', $filters[ 'q' ] ), 0 );
            }
            $query->where( DB::raw( 'LOWER(container_number)' ), 'LIKE', '%' . strtolower( $filters[ 'q' ] ) . '%' );
        }

        if ( !empty( $filters[ 'id' ] ) ) {
            $query->where( 'id', $filters[ 'id' ] );
        }

        $query->groupBy( 'id' );

        $limit = Arr::get( $filters, 'limit', 20 );

        return $limit != '-1' ? $query->paginate( $limit ) : $query->get();
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function search( $filters = [] )
    {
        $query = Export::with( [ 'customer', 'houstan_custom_cover_letter', 'dock_receipt', 'export_images', 'empty_container_photos', 'loading_photos', 'loaded_photos', 'vehicles' ] );

        if ( !empty( $filters[ 'export_global_search' ] ) ) {
            $query->where( function ( $q ) use ( $filters ) {
                $q->where( 'booking_number', $filters[ 'export_global_search' ] )
                    ->orWhere( DB::raw( 'LOWER(ar_number)' ), 'LIKE', '%' . strtolower( $filters[ 'export_global_search' ] ) . '%' )
                    ->orWhere( DB::raw( 'LOWER(container_number)' ), 'LIKE', '%' . strtolower( $filters[ 'export_global_search' ] ) . '%' );
            } );
        }

        return $query->first();
    }

    public function trackingUrl( $container )
    {
        if ( !$container instanceof Export ) {
            $container = Export::where( 'container_number', urldecode( $container ) )->first();
        }

        $url = "";

        if ( $container ) {
            switch ( $container->streamship_line ) {
                case StreamshipLine::MAERSK:
                case StreamshipLine::APM_TERMINALS:
                    $url = "https://www.maersk.com/tracking/" . $container->container_number;
                    break;
                case StreamshipLine::HMM:
                    $url = "https://www.hmm21.com/cms/business/ebiz/trackTrace/trackTrace/index.jsp?type=1&number=" . $container->container_number . "&is_quick=Y&quick_params=";
                    break;
                case StreamshipLine::MSC:
                    $url = "https://www.msc.com/en/track-a-shipment?params=" . urlencode( base64_encode( "trackingNumber=" . $container->container_number . "&trackingMode=0" ) );
                    break;
                case StreamshipLine::HAPAG_LLOYD:
                    $url = "https://www.hapag-lloyd.com/en/online-business/track/track-by-container-solution.html?container=" . $container->container_number;
                    break;
                case StreamshipLine::YANG_MING:
//                    $url = "https://www.yangming.com/e-service/track_trace/track_trace_cargo_tracking.aspx";
                    $url = "https://www.yangming.com/en/esolution/cargo_tracking?service=". $container->container_number;
                    break;
                case StreamshipLine::ONE:
                    $url = "https://ecomm.one-line.com/ecom/CUP_HOM_3301.do?redir=Y&ctrack-field=" . $container->container_number . "&sessLocale=en&trakNoParam=" . $container->container_number;
                    break;
                case StreamshipLine::EVERGREEN:
//                    $url = "https://w8shipping.com/query/evergreen.php?nr=". $container->container_number ."&booking=" . $container->booking_number;
                    $url = "https://ct.shipmentlink.com/servlet/TDB1_CargoTracking.do";
                    break;
                case StreamshipLine::CMA_CGM:
                    $url = "https://www.cma-cgm.com/ebusiness/tracking";
                    break;
                case StreamshipLine::APL:
                    $url = "https://www.apl.com/ebusiness/tracking/search";
                    break;
            }
        }

        return $url;
    }
}
