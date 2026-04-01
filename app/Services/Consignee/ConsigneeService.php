<?php


namespace App\Services\Consignee;


use App\Enums\Roles;
use App\Models\Consignee;
use App\Services\BaseService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class ConsigneeService extends BaseService {

	/**
	 * @param array $filters
	 *
	 * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Builder
	 */
	public function all( array $filters = [] ) {
        $query = Consignee::query();

        if ( ! empty( $filters[ 'select' ] ) ) {
            $query->select( $filters[ 'select' ] );
        } else {
            $query->with( 'customer' );
        }

        if ( ! empty( $filters[ 'status' ] ) ) {
            $query->where( 'status', $filters[ 'status' ] );
        }

        if ( optional( auth()->user() )->role == Roles::CUSTOMER ) {
            $filters[ 'customer_user_id' ] = auth()->user()->id;
        }
        if ( ! empty( $filters[ 'customer_user_id' ] ) ) {
            $query->where( function ( $q ) use ( $filters ) {
                $q->where( 'customer_user_id', $filters[ 'customer_user_id' ] );
                $q->orWhereNull( 'customer_user_id' );
            } );
        }

        if ( ! empty( $filters[ 'consignee_name' ] ) ) {
            $query->where( DB::raw( 'LOWER(consignee_name)' ), 'LIKE', '%' . strtolower( $filters[ 'consignee_name' ] ) . '%' );
        }

        if ( ! empty( $filters[ 'city_id' ] ) ) {
            $query->where( 'city_id', $filters[ 'city_id' ] );
        }

        if ( ! empty( $filters[ 'state_id' ] ) ) {
            $query->where( 'state_id', $filters[ 'state_id' ] );
        }

        if ( ! empty( $filters[ 'country_id' ] ) ) {
            $query->where( 'country_id', $filters[ 'country_id' ] );
        }

        if ( ! empty( $filters[ 'phone' ] ) ) {
            $query->where( DB::raw( 'phone' ), 'LIKE', '%' . strtolower( $filters[ 'phone' ] ) . '%' );
        }

        if ( ! empty( $filters[ 'consignee_global_search' ] ) ) {
            $query->where( function ( $q ) use ( $filters ) {
                $q->where( 'customer_user_id', $filters[ 'consignee_global_search' ] )
                    ->orWhere( DB::raw( 'LOWER(consignee_name)' ), 'LIKE', '%' . strtolower( $filters[ 'consignee_global_search' ] ) . '%' )
                    ->orWhere( DB::raw( 'LOWER(phone)' ), 'LIKE', '%' . strtolower( $filters[ 'consignee_global_search' ] ) . '%' );
            } );
        }

        if(auth()->user()->role == 2) {
            if( ! in_array(16, optional(auth()->user())->locations ?? [] ) ) {
                $query->whereNotIn('customer_user_id', explode(",", env('CHINA_CUSTOMER_USER_IDS')));
            }
        }else if(  ! in_array(auth()->user()->role,  [0]  ) ) {
            $query->whereNotIn('customer_user_id', explode(",", env('CHINA_CUSTOMER_USER_IDS')));
        }

        $query->orderBy( Arr::get( $filters, 'order_by_column', 'id' ), Arr::get( $filters, 'order_by', 'desc' ) );
        $limit = Arr::get( $filters, 'limit', 20 );

        if ( $limit != -1 ) {
            return $query->paginate( $limit );
        }

        return Arr::get($filters, 'query_only', false) ? $query : $query->get();
    }

	/**
	 * @param $id
	 *
	 * @return mixed
	 */
	public function getById( $id )
    {
        $query = Consignee::with( [ 'customer', 'country', 'state', 'city' ] );

        if ( optional( auth()->user() )->role == Roles::CUSTOMER ) {
            $query->where( function ( $q ) {
                $q->where( 'customer_user_id', auth()->user()->id );
                $q->orWhereNull( 'customer_user_id' );
            } );
        }

        return $query->find( $id );
    }

	/**
	 * @param array $data
	 *
	 * @return mixed
	 */
	public function store( array $data ) {
		return $this->saveConsignee( $data );
	}

	/**
	 * @param $id
	 * @param array $data
	 *
	 * @return mixed
	 */
	public function update( $id, array $data ) {
		return $this->saveConsignee( $data, $id );
	}

	/**
	 * @param $id
	 *
	 * @return mixed
	 */
	public function destroy( $id ) {
		return Consignee::find( $id )->delete();
	}

	/**
	 * @param $data
	 * @param null $id
	 *
	 * @return mixed
	 */
	private function saveConsignee( $data, $id = null )
    {
        unset( $data[ 'version_id' ] );

        $consignee = Consignee::findOrNew( $id );
        $consignee->fill( $data );
        $consignee->save();

        return $consignee;
    }
}
