<?php


namespace App\Services\Yard;


use App\Models\Yard;
use App\Services\BaseService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class YardService extends BaseService
{
    public function all ( array $filters = [] )
    {
        $query = Yard::with( 'location' );

        if ( isset( $filters[ 'status' ] ) ) {
            $query->where( 'status', $filters[ 'status' ] );
        }


        // For China yard condition
        if(auth()->user()->role == 2) {
            if( ! in_array(16, optional(auth()->user())->locations ?? [] ) ) {
                $query->where('location_id', '!=', 16);
            }
        }else if(auth()->user()->role == 3){
            if( in_array(auth()->user()->id,  explode(",", env('CHINA_CUSTOMER_USER_IDS'))) ) {
                $query->where('location_id', '=', 16);
            }else {
                $query->where('location_id', '!=', 16);
            }
        } else if(  ! in_array(auth()->user()->role,  [0]  ) ) {
            $query->where('location_id', '!=', 16);
        }

        if ( ! empty( $filters[ 'location_id' ] ) ) {
            $query->where( 'location_id', $filters[ 'location_id' ] );
        }


        if ( ! empty( $filters[ 'name' ] ) ) {
            $query->where( DB::raw( 'LOWER(name)' ), 'LIKE', '%' . strtolower( $filters[ 'name' ] ) . '%' );
        }

        $limit = Arr::get( $filters, 'limit', 20 );

        return $limit != '-1' ? $query->paginate( $limit ) : $query->paginate( 1000 );
    }

    public function getById ( $id )
    {
        return Yard::with('location')->find( $id );
    }

    public function store ( array $data )
    {
        return $this->saveYard( $data );
    }

    public function update ( $id, array $data )
    {
        return $this->saveYard( $data, $id );
    }

    public function destroy ( $id )
    {
        return Yard::find( $id )->delete();
    }

    private function saveYard ( $data, $id = null )
    {
        $state = Yard::findOrNew( $id );
        $state->fill( $data );
        $state->save();

        return $state;
    }
}
