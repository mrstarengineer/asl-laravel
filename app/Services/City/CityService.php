<?php


namespace App\Services\City;


use App\Models\City;
use App\Services\BaseService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CityService extends BaseService
{
    public function all ( array $filters = [] )
    {
        $query = City::with('state');

        if ( isset( $filters[ 'status' ] ) ) {
            $query->where( 'status', $filters[ 'status' ] );
        }

        if ( ! empty( $filters[ 'state_id' ] ) ) {
            $query->where( 'state_id', $filters[ 'state_id' ] );
        }

        if ( ! empty( $filters[ 'name' ] ) ) {
            $query->where( DB::raw( 'LOWER(name)' ), 'LIKE', '%' . strtolower( $filters[ 'name' ] ) . '%' );
        }

        if ( ! empty( $filters[ 'short_code' ] ) ) {
            $query->where( DB::raw( 'LOWER(short_code)' ), 'LIKE', '%' . strtolower( $filters[ 'short_code' ] ) . '%' );
        }

        $limit = Arr::get( $filters, 'limit', 20 );

        return $limit != '-1' ? $query->paginate( $limit ) : $query->paginate( 1000 );
    }

    public function getById ( $id )
    {
        return City::with('state.country')->find( $id );
    }

    public function store ( array $data )
    {
        return $this->saveCity( $data );
    }

    public function update ( $id, array $data )
    {
        return $this->saveCity( $data, $id );
    }

    public function destroy ( $id )
    {
        return City::find( $id )->delete();
    }

    private function saveCity ( $data, $id = null )
    {
        $city = City::findOrNew( $id );
        $city->fill( $data );
        $city->slug = Str::slug( $city->name );
        $city->save();

        return $city;
    }
}
