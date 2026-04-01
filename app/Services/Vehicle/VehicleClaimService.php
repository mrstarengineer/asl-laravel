<?php


namespace App\Services\Vehicle;


use App\Enums\ClaimPhotoType;
use App\Enums\ClaimType;
use App\Enums\ReadStatus;
use App\Enums\Roles;
use App\Models\ClaimImage;
use App\Models\VehicleClaim;
use App\Services\BaseService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class VehicleClaimService extends BaseService
{
    public function all ( array $filters = [] )
    {
        $type = Arr::get( $filters, 'type', ClaimType::DAMAGE_CLAIM );
        $query = VehicleClaim::query()->with( [ 'vehicle.customer', 'vehicle.location', 'customer', ] )->where( 'type', $type );

        if ( ! empty( $filters[ 'vehicle_id' ] ) ) {
            $query->where( 'vehicle_id', $filters[ 'vehicle_id' ] );
        }

        if ( ! empty( $filters[ 'export_id' ] ) ) {
            $query->where( 'export_id', $filters[ 'export_id' ] );
        }

        if ( isset( $filters[ 'claim_status' ] ) ) {
            $query->where( 'claim_status', $filters[ 'claim_status' ] );
        }

        if ( in_array( optional( auth()->user() )->role, [ Roles::LOCATION_ADMIN, Roles::LOCATION_VIEW_ADMIN, Roles::LOCATION_RESTRICTED_ADMIN ] ) ) {
            $query->whereHas('vehicle', function ($query) {
                $query->whereIn( 'location_id', auth()->user()->locations );

                if( optional( auth()->user() )->customers ) {
                    $query->whereHas('customer', function ( $q ) {
                        $q->whereIn('legacy_customer_id', auth()->user()->customers );
                    });
                }
            });
        }

        if ( optional(auth()->user())->role == Roles::CUSTOMER ) {
            $query->where( 'customer_user_id', auth()->user()->id );
        } elseif ( ! empty( $filters[ 'customer_user_id' ] ) ) {
            $query->where( 'customer_user_id', $filters[ 'customer_user_id' ] );
        }

        if ( ! empty( $filters[ 'approved_date' ] ) ) {
            $query->where( DB::raw( 'approved_date' ), 'LIKE', '%' . $filters[ 'approved_date' ] . '%' );
        }

        if ( ! empty( $filters[ 'vehicle_part' ] ) ) {
            $query->where( 'vehicle_part', $filters[ 'vehicle_part' ] );
        }

        if ( ! empty( $filters[ 'other_parts' ] ) ) {
            $query->where( DB::raw( 'LOWER(other_parts)' ), 'LIKE', '%' . strtolower( $filters[ 'other_parts' ] ) . '%' );
        }

        if ( ! empty( $filters[ 'remarks' ] ) ) {
            $query->where( DB::raw( 'LOWER(remarks)' ), 'LIKE', '%' . strtolower( $filters[ 'remarks' ] ) . '%' );
        }

        if ( ! empty( $filters[ 'vin' ] ) || ! empty( $filters[ 'location_id' ] ) ) {
            $query->whereHas( 'vehicle', function ( $q ) use ( $filters ) {
                if ( ! empty( $filters[ 'vin' ] ) ) {
                    $q->where( DB::raw( 'LOWER(vin)' ), 'LIKE', '%' . strtolower( $filters[ 'vin' ] ) . '%' );
                }
                if ( ! empty( $filters[ 'location_id' ] ) ) {
                    $q->where( 'location_id', $filters[ 'location_id' ] );
                }
            } );
        }

        $limit = Arr::get( $filters, 'limit', 20 );

        return $limit != '-1' ? $query->paginate( $limit ) : $query->get();
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function getById ( $id )
    {
        return VehicleClaim::with([ 'vehicle.location', 'customer', 'admin_photos', 'customer_photos' ])->find( $id );
    }

    public function store ( array $data )
    {
        return $this->saveDamageClaim( $data );
    }

    /**
     * @param $id
     * @param array $data
     *
     * @return mixed
     */
    public function update( $id, array $data ) {
        $this->removeClaimImages($id, Arr::get( $data, 'customer_photos', [] ));
        $this->removeClaimImages($id, Arr::get( $data, 'admin_photos', [] ), ClaimPhotoType::ADMIN_PHOTO);
        return $this->saveDamageClaim( $data, $id );
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function destroy( $id ) {
        return VehicleClaim::find( $id )->delete();
    }

    private function saveDamageClaim ( array $data, $id = null )
    {
        $data['claim_status'] = is_null( Arr::get( $data, 'claim_status' ) ) ? 10 : Arr::get( $data, 'claim_status' );
        $claim = VehicleClaim::findOrNew( $id );
        $claim->fill( $data );
        $claim->save();

        $this->saveClaimImages( Arr::get( $data, 'customer_photos', [] ), $claim->id );
        $this->saveClaimImages( Arr::get( $data, 'admin_photos', [] ), $claim->id, ClaimPhotoType::ADMIN_PHOTO );

        return $claim;
    }

    private function removeClaimImages ( $claimId, $newDocs, $type = ClaimPhotoType::CUSTOMER_PHOTO )
    {
        $ids = ClaimImage::where( [
            'claim_id' => $claimId,
            'type'     => $type,
        ] )->whereNotIn( 'id', collect( $newDocs )->reject( function ( $url ) {
            return filter_var($url, FILTER_VALIDATE_URL);
        })->pluck( 'id' )->toArray() )
            ->pluck( 'id' )
            ->toArray();

        ClaimImage::whereIn( 'id', $ids )->delete();
    }

    private function saveClaimImages ( $documents, $claimId, $type = ClaimPhotoType::CUSTOMER_PHOTO )
    {
        foreach ( $documents as $url ) {
            if ( filter_var( $url, FILTER_VALIDATE_URL ) ) {
                $invoiceDoc = new ClaimImage();
                $invoiceDoc->image = str_replace( env( 'AWS_S3_BASE_URL' ), '', $url );
                $invoiceDoc->claim_id = $claimId;
                $invoiceDoc->type = $type;
                $invoiceDoc->save();
            }
        }
    }

    public function adminUnreadCount ( $type = ClaimType::DAMAGE_CLAIM )
    {
        $query = VehicleClaim::where( [
            'admin_view' => ReadStatus::UNREAD,
            'type'       => $type,
        ] );

        if ( in_array( optional( auth()->user() )->role, [ Roles::LOCATION_ADMIN, Roles::LOCATION_VIEW_ADMIN, Roles::LOCATION_RESTRICTED_ADMIN ] ) ) {
            $query->whereHas('vehicle', function ($query) {
                $query->whereIn( 'location_id', auth()->user()->locations );

                if( optional( auth()->user() )->customers ) {
                    $query->whereHas('customer', function ( $q ) {
                        $q->whereIn('legacy_customer_id', auth()->user()->customers );
                    });
                }
            });
        }

        return $query->count();
    }

}
