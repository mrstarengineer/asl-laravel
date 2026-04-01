<?php


namespace App\Services\VehicleWeight;


use App\Models\VehicleWeight;
use App\Services\BaseService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class VehicleWeightService extends BaseService {

	/**
	 * @param array $filters
	 *
	 * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
	 */
	public function all( array $filters = [] ) {
		$query = VehicleWeight::query();

		if ( ! empty( $filters['year'] ) ) {
			$query->where( 'year', $filters['year'] );
		}

        if ( ! empty( $filters[ 'maker' ] ) ) {
            $query->where( DB::raw( 'LOWER(maker)' ), 'LIKE', '%' . strtolower( $filters[ 'maker' ] ) . '%' );
        }

        if ( ! empty( $filters[ 'model' ] ) ) {
            $query->where( DB::raw( 'LOWER(model)' ), 'LIKE', '%' . strtolower( $filters[ 'model' ] ) . '%' );
        }

        if ( ! empty( $filters[ 'weight' ] ) ) {
            $query->where( DB::raw( 'LOWER(weight)' ), 'LIKE', '%' . strtolower( $filters[ 'weight' ] ) . '%' );
        }

        $query->orderBy( Arr::get( $filters, 'order_by_column', 'id' ), Arr::get( $filters, 'order_by', 'desc' ) );
		$limit = Arr::get( $filters, 'limit', 20 );

		return $limit != '-1' ? $query->paginate( $limit ) : $query->get();
	}

	/**
	 * @param $id
	 *
	 * @return mixed
	 */
	public function getById( $id ) {
		return VehicleWeight::find( $id );
	}

	/**
	 * @param array $data
	 *
	 * @return mixed
	 */
	public function store( array $data ) {
		return $this->saveVehicleWeight( $data );
	}

	/**
	 * @param $id
	 * @param array $data
	 *
	 * @return mixed
	 */
	public function update( $id, array $data ) {
		return $this->saveVehicleWeight( $data, $id );
	}

	/**
	 * @param $id
	 *
	 * @return mixed
	 */
	public function destroy( $id ) {
		return VehicleWeight::find( $id )->delete();
	}

	/**
	 * @param $data
	 * @param null $id
	 *
	 * @return mixed
	 */
	private function saveVehicleWeight( $data, $id = null ) {
		$location = VehicleWeight::findOrNew( $id );
		$location->fill( $data );
		$location->save();

		return $location;
	}

    public function vehicleWeight ( $filters = [] )
    {
        $query = VehicleWeight::query();

        if ( ! empty( $filters['year'] ) ) {
            $query->where( 'year', $filters['year'] );
        }

        if ( ! empty( $filters['make'] ) ) {
            $query->where( 'maker', $filters['make'] );
        }

        if ( ! empty( $filters['model'] ) ) {
            $query->where( 'model', $filters['model'] );
        }

        return data_get($query->first(), 'weight');
	}
}
