<?php


namespace App\Services\Country;


use App\Models\Country;
use App\Services\BaseService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CountryService extends BaseService
{
    public function all ( array $filters = [] )
    {
        $query = Country::query();

        if ( isset( $filters[ 'status' ] ) ) {
            $query->where( 'status', $filters[ 'status' ] );
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
        return Country::find( $id );
    }

    public function store ( array $data )
    {
        return $this->saveCountry( $data );
    }

    public function update ( $id, array $data )
    {
        return $this->saveCountry( $data, $id );
    }

    public function destroy ( $id )
    {
        return Country::find( $id )->delete();
    }

    private function saveCountry ( $data, $id = null )
    {
        $country = Country::findOrNew( $id );
        $country->fill( $data );
        $country->slug = Str::slug( $country->name );
        $country->save();

        return $country;
    }
}
