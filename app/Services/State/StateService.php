<?php


namespace App\Services\State;


use App\Models\State;
use App\Services\BaseService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StateService extends BaseService
{
    public function all ( array $filters = [] )
    {
        $query = State::with( 'country' );

        if ( isset( $filters[ 'status' ] ) ) {
            $query->where( 'status', $filters[ 'status' ] );
        }

        if ( ! empty( $filters[ 'country_id' ] ) ) {
            $query->where( 'country_id', $filters[ 'country_id' ] );
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
        return State::with('country')->find( $id );
    }

    public function store ( array $data )
    {
        return $this->saveState( $data );
    }

    public function update ( $id, array $data )
    {
        return $this->saveState( $data, $id );
    }

    public function destroy ( $id )
    {
        return State::find( $id )->delete();
    }

    private function saveState ( $data, $id = null )
    {
        $state = State::findOrNew( $id );
        $state->fill( $data );
        $state->slug = Str::slug( $state->name );
        $state->save();

        return $state;
    }
}
