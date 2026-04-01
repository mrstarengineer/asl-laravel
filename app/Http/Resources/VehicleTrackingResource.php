<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class VehicleTrackingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray( $request )
    {
        return [
            'vehicle_type'     => $this->vehicle_type,
            'year'             => $this->year,
            'color'            => $this->color,
            'model'            => $this->model,
            'make'             => $this->make,
            'vin'              => $this->vin,
            'lot_number'       => $this->lot_number,
            'towed_from'       => $this->towed_from,
            'status_name'      => $this->getStatusNameProperty( $this->status ),
            "container_number" => optional( $this->export )->container_number,
            'keys_name'        => $this->keys == 1 ? 'Yes' : 'No',
            'location_name'    => data_get( $this, 'location.name' ),
            'photos'           => $this->getPhotoUrlsProperty( $this->vehicle_image ),

        ];
    }

    private function getStatusNameProperty( $value ): string
    {
        return array_key_exists( $value, trans( 'vehicle.statuses' ) ) ? trans( 'vehicle.statuses.' . $value ) : '';
    }

    public function getPhotoUrlsProperty( $photos ): array
    {
        return collect( $photos )->reject( function ( $item ) {
            return !Storage::exists( $item[ 'name' ] );
        } )->map( function ( $item ) {
            return [
                'thumbnail' => filter_var( $item[ 'thumbnail' ], FILTER_VALIDATE_URL ) === false ? Storage::url( $item[ 'thumbnail' ] ) : $item[ 'thumbnail' ],
                'url'       => filter_var( $item[ 'name' ], FILTER_VALIDATE_URL ) === false ? Storage::url( $item[ 'name' ] ) : $item[ 'name' ],
            ];
        } )->values()->all();
    }
}
