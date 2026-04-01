<?php

namespace App\Transformer;

use App\Enums\NoteStatus;
use App\Enums\Roles;
use App\Enums\VehicleStatus;
use App\Services\Export\ExportService;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class VehicleTransformer extends \Nahid\Presento\Transformer
{
    public function getDamageClaimProperty ( $values )
    {
        return $values ? 'Yes' : 'No';
    }

    public function getClaimStatusProperty ( $values )
    {
        return array_key_exists( Arr::get( $values, '0.claim_status' ), trans( 'vehicle_claim.claim_statuses' ) ) ? trans( 'vehicle_claim.claim_statuses.' . Arr::get( $values, '0.claim_status' ) ) : '';
    }

    public function getKeysNameProperty ( $value ): string
    {
        return ( $value == 1 ) ? 'Yes' : 'No';
    }

    public function getKeysProperty ( $value ): string
    {
        return ( $value == 1 ) ? '1' : '0';
    }

    public function getHybridProperty ( $value ): string
    {
        return ( $value == 1 ) ? '1' : '2';
    }

    public function getTitleTypeNameProperty ( $value ): string
    {
        switch ( $value ) {
            case 1 :
                $val = 'EXPORTABLE';
                break;
            case 2 :
                $val = 'PENDING';
                break;
            case 3 :
                $val = 'BOS';
                break;
            case 4 :
                $val = 'LIEN';
                break;
            case 5 :
                $val = 'MV907';
                break;
            case 6 :
                $val = 'REJECTED';
                break;
            case 0:
            default:
                $val = 'No TITLE';
                break;
        }

        return $val;
    }

    public function getStatusNameProperty($value): string
    {
        return array_key_exists( $value, trans( 'vehicle.statuses' ) ) ? trans( 'vehicle.statuses.' . $value ) : '';
    }

    public function getPhotoProperty ( $photo )
    {
        if ( empty( $photo ) ) {
            return url( 'images/no-image.png' );
        }

        return filter_var( $photo, FILTER_VALIDATE_URL ) === false ? Storage::url( $photo ) : $photo;
    }

    public function getPhotosProperty ( $photos )
    {
        return collect( $photos )->reject( function ( $item ) {
            return ! Storage::exists( $item[ 'name' ] );
        })->map( function ( $item ) {
            return [
                'id'        => $item['id'],
                'name'      => basename(filter_var($item['name'], FILTER_VALIDATE_URL) === false ? Storage::url($item['name']) : $item['name']),
                'thumbnail' => filter_var($item['thumbnail'], FILTER_VALIDATE_URL) === false ? Storage::url($item['thumbnail']) : $item['thumbnail'],
                'url'       => filter_var($item['name'], FILTER_VALIDATE_URL) === false ? Storage::url($item['name']) : $item['name'],
                'type'      => $item['type'],
                'size'      => $item['size'],
            ];
        } )->values()->all();

//        return collect( $photos )->map( function ( $item ) {
//            return [
//                'id'        => $item['id'],
//                'name'      => basename(filter_var($item['name'], FILTER_VALIDATE_URL) === false ? Storage::url($item['name']) : $item['name']),
//                'thumbnail' => filter_var($item['thumbnail'], FILTER_VALIDATE_URL) === false ? Storage::url($item['thumbnail']) : $item['thumbnail'],
//                'url'       => filter_var($item['name'], FILTER_VALIDATE_URL) === false ? Storage::url($item['name']) : $item['name'],
//                'type'      => $item['type'],
//                'size'      => $item['size'],
//            ];
//        } )->values()->all();
    }

    public function getPhotoUrlsProperty ( $photos ): \Illuminate\Support\Collection
    {
        return collect( $photos )->map( function ( $item ) {
            return filter_var( $item[ 'thumbnail' ], FILTER_VALIDATE_URL ) === false ? Storage::url( $item[ 'thumbnail' ] ) : $item[ 'thumbnail' ];
        } );
    }

    public function getAuctionPhotosProperty ( $photos )
    {
        return collect( $photos )->reject( function ( $item ) {
            return ! Storage::exists( $item[ 'name' ] );
        })->map( function ( $item ) {
            return [
                'id'   => $item[ 'id' ],
                'name' => basename( filter_var( $item[ 'name' ], FILTER_VALIDATE_URL ) === false ? Storage::url( $item[ 'name' ] ) : $item[ 'name' ] ),
                'thumbnail' => filter_var($item['thumbnail'], FILTER_VALIDATE_URL) === false ? Storage::url($item['thumbnail']) : $item['thumbnail'],
                'url'  => filter_var( $item[ 'name' ], FILTER_VALIDATE_URL ) === false ? Storage::url( $item[ 'name' ] ) : $item[ 'name' ],
                'type' => $item[ 'type' ],
                'size' => $item[ 'size' ],
            ];
        } )->values()->all();

//        return collect( $photos )->map( function ( $item ) {
//            return [
//                'id'   => $item[ 'id' ],
//                'name' => basename( filter_var( $item[ 'name' ], FILTER_VALIDATE_URL ) === false ? Storage::url( $item[ 'name' ] ) : $item[ 'name' ] ),
//                'thumbnail' => filter_var($item['thumbnail'], FILTER_VALIDATE_URL) === false ? Storage::url($item['thumbnail']) : $item['thumbnail'],
//                'url'  => filter_var( $item[ 'name' ], FILTER_VALIDATE_URL ) === false ? Storage::url( $item[ 'name' ] ) : $item[ 'name' ],
//                'type' => $item[ 'type' ],
//                'size' => $item[ 'size' ],
//            ];
//        } )->values()->all();
    }

    public function getPickupPhotosProperty ( $photos )
    {
        return collect( $photos )->reject( function ( $item ) {
            return ! Storage::exists( $item[ 'name' ] );
        })->map( function ( $item ) {
            return [
                'id'   => $item[ 'id' ],
                'name' => basename( filter_var( $item[ 'name' ], FILTER_VALIDATE_URL ) === false ? basename( Storage::url( $item[ 'name' ] ) ) : basename( $item[ 'name' ] ) ),
                'thumbnail' => filter_var($item['thumbnail'], FILTER_VALIDATE_URL) === false ? Storage::url($item['thumbnail']) : $item['thumbnail'],
                'url'  => filter_var( $item[ 'name' ], FILTER_VALIDATE_URL ) === false ? Storage::url( $item[ 'name' ] ) : $item[ 'name' ],
                'type' => $item[ 'type' ],
                'size' => $item[ 'size' ],
            ];
        } )->values()->all();

//        return collect( $photos )->map( function ( $item ) {
//            return [
//                'id'   => $item[ 'id' ],
//                'name' => basename( filter_var( $item[ 'name' ], FILTER_VALIDATE_URL ) === false ? basename( Storage::url( $item[ 'name' ] ) ) : basename( $item[ 'name' ] ) ),
//                'thumbnail' => filter_var($item['thumbnail'], FILTER_VALIDATE_URL) === false ? Storage::url($item['thumbnail']) : $item['thumbnail'],
//                'url'  => filter_var( $item[ 'name' ], FILTER_VALIDATE_URL ) === false ? Storage::url( $item[ 'name' ] ) : $item[ 'name' ],
//                'type' => $item[ 'type' ],
//                'size' => $item[ 'size' ],
//            ];
//        } )->values()->all();
    }

    public function getArrivedPhotosProperty ( $photos )
    {
        return collect( $photos )->reject( function ( $item ) {
            return ! Storage::exists( $item[ 'name' ] );
        })->map( function ( $item ) {
            return [
                'id'   => $item[ 'id' ],
                'name' => basename( filter_var( $item[ 'name' ], FILTER_VALIDATE_URL ) === false ? Storage::url( $item[ 'name' ] ) : $item[ 'name' ] ),
                'thumbnail' => filter_var($item['thumbnail'], FILTER_VALIDATE_URL) === false ? Storage::url($item['thumbnail']) : $item['thumbnail'],
                'url'  => filter_var( $item[ 'name' ], FILTER_VALIDATE_URL ) === false ? Storage::url( $item[ 'name' ] ) : $item[ 'name' ],
                'type' => $item[ 'type' ],
                'size' => $item[ 'size' ],
            ];
        } )->values()->all();

//        return collect( $photos )->map( function ( $item ) {
//            return [
//                'id'   => $item[ 'id' ],
//                'name' => basename( filter_var( $item[ 'name' ], FILTER_VALIDATE_URL ) === false ? Storage::url( $item[ 'name' ] ) : $item[ 'name' ] ),
//                'thumbnail' => filter_var($item['thumbnail'], FILTER_VALIDATE_URL) === false ? Storage::url($item['thumbnail']) : $item['thumbnail'],
//                'url'  => filter_var( $item[ 'name' ], FILTER_VALIDATE_URL ) === false ? Storage::url( $item[ 'name' ] ) : $item[ 'name' ],
//                'type' => $item[ 'type' ],
//                'size' => $item[ 'size' ],
//            ];
//        } )->values()->all();
    }

    public function getLoadingTypeProperty ( $value )
    {
        switch ( $value ) {
            case 1 :
                $val = 'FULL';
                break;
            case 2 :
                $val = 'MIX';
                break;
            default:
                $val = '';
                break;
        }

        return $val;
    }

    public function getCreatedAtProperty ( $value )
    {
        return date( 'Y-m-d', strtotime( $value ) );
    }

    public function getUpdatedAtProperty ( $value )
    {
        return date( 'Y-m-d', strtotime( $value ) );
    }

    public function getStateNameProperty ( $state )
    {
        return Arr::get( $state, 'name', '' );
    }

    public function getVehicleFeaturesProperty ( $values )
    {
        return collect( $values )->pluck( 'features_id', 'features_id' );
    }

    public function getVehicleConditionsProperty ( $values )
    {
        return collect( $values )->pluck( 'value', 'condition_id' );
    }

    public function getVehicleDocumentsProperty ( $values )
    {
        $count = 0;
        return collect( $values )->reject( function ( $item ) {
            return ! Storage::exists( $item[ 'name' ] );
        })->map( function ( $item ) use ( &$count ) {
            return [
                'id'    => $item['id'],
                'label' => 'Doc-' . ++$count,
                'url'   => filter_var( $item[ 'name' ], FILTER_VALIDATE_URL ) === false ? Storage::url( $item[ 'name' ] ) : $item[ 'name' ],
                'type'  => $item[ 'type' ],
                'size'  => $item[ 'size' ],
            ];
        } )->values()->all();
    }

    public function getInvoicePhotosProperty ( $values )
    {
        $count = 0;
        return collect( $values )->reject( function ( $item ) {
            return ! Storage::exists( $item[ 'name' ] );
        })->map( function ( $item ) use ( &$count ) {
            return [
                'id'    => $item[ 'id' ],
                'label' => 'Invoice-' . ++$count,
                'url'   => filter_var( $item[ 'name' ], FILTER_VALIDATE_URL ) === false ? Storage::url( $item[ 'name' ] ) : $item[ 'name' ],
                'type'  => $item[ 'type' ],
                'size'  => $item[ 'size' ],
            ];
        } )->values()->all();
    }

    public function getNoteStatusProperty ( $totalNotes )
    {
        $data = $this->getData();
        $noteStatus = Arr::get( $data, 'notes_status', 0 );
        if ( is_null( $noteStatus ) ) {
            $noteStatus = 0;
        }

        $response = [ 'label' => 'Notes', 'class' => 'link_blue', 'value' => $noteStatus ];
        if ( $noteStatus == NoteStatus::CLOSED ) {
            $response = [ 'label' => 'Closed', 'class' => 'link_green', 'value' => $noteStatus ];
        } elseif ( $noteStatus == NoteStatus::OPEN ) {
            $response = [ 'label' => 'Open', 'class' => 'link_red', 'value' => $noteStatus ];
        }

        return $response;
    }

    public function getVisibleExportButtonProperty ()
    {
        $data = $this->getData();
        if ( Arr::get( $data, 'export_id' ) && in_array( Arr::get( $data, 'status' ), [ VehicleStatus::MANIFEST, VehicleStatus::SHIPPED, VehicleStatus::ARRIVED ] ) ) {
            return true;
        }

        return false;
    }

    public function getVisibleClaimButtonProperty ()
    {
        $data = $this->getData();
        if ( optional(auth()->user())->role == Roles::CUSTOMER && in_array( Arr::get( $data, 'status' ), [ VehicleStatus::ON_HAND, VehicleStatus::MANIFEST, VehicleStatus::SHIPPED, VehicleStatus::ARRIVED ] ) ) {
            return true;
        }

        return false;
    }

    public function getVisibleStorageButtonProperty ()
    {
        $data = $this->getData();
        if ( optional(auth()->user())->role == Roles::CUSTOMER && in_array( Arr::get( $data, 'status' ), [ VehicleStatus::ON_HAND, VehicleStatus::MANIFEST, VehicleStatus::SHIPPED, VehicleStatus::ARRIVED ] ) ) {
            return true;
        }

        return false;
    }

    public function getVisibleKeyMissingButtonProperty ()
    {
        $data = $this->getData();
        if ( optional(auth()->user())->role == Roles::CUSTOMER && Arr::get( $data, 'keys' ) == 1 && in_array( Arr::get( $data, 'status' ), [ VehicleStatus::ON_HAND, VehicleStatus::MANIFEST, VehicleStatus::SHIPPED, VehicleStatus::ARRIVED ] ) ) {
            return true;
        }

        return false;
    }

    public function getRowColorProperty ($status): string
    {
        $code = '';
        switch ($status) {
            case VehicleStatus::ARRIVED:
                $code = '#ffb631';
                break;
            case VehicleStatus::ON_HAND:
                $code = '#DAF4F0';
                break;
            case VehicleStatus::MANIFEST:
                $code = '#FFFF66';
                break;
            case VehicleStatus::ON_THE_WAY:
                $code = '#DF9D9D';
                break;
            case VehicleStatus::SHIPPED:
                $code = '#66FF99';
                break;
        }

        return $code;
    }

    public function getContainerTrackingUrlProperty ( $value ): string
    {
        return app( ExportService::class )->trackingUrl( $value );
    }

    public function getCarPaidProperty( $value ) : string
    {
        return Carbon::parse($value)->addDay()->format('Y-m-d');
    }
}

