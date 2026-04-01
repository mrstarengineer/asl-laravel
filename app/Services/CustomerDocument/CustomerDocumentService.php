<?php


namespace App\Services\CustomerDocument;


use App\Models\CustomerDocument;
use App\Services\BaseService;
use Illuminate\Support\Arr;

class CustomerDocumentService extends BaseService {

	/**
	 * @param array $filters
	 *
	 * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
	 */
	public function all( array $filters = [] ) {
		$query = CustomerDocument::query();

		if ( ! empty( $filters['status'] ) ) {
			$query->where( 'status', $filters['status'] );
		}

		if ( ! empty( $filters['month'] ) ) {
			$query->where( 'month', $filters['month'] );
		}

		if ( ! empty( $filters['created_at'] ) ) {
			$query->where( 'created_at', $filters['created_at'] );
		}

		if ( ! empty( $filters['updated_at'] ) ) {
			$query->where( 'updated_at', $filters['updated_at'] );
		}

		if ( ! empty( $filters['pricing_type'] ) ) {
			$query->where( 'pricing_type', $filters['pricing_type'] );
		}

		$limit = Arr::get( $filters, 'limit', 20 );

		return $limit != '-1' ? $query->paginate( $limit ) : $query->get();
	}

	/**
	 * @param $id
	 *
	 * @return mixed
	 */
	public function getById( $id ) {
		return CustomerDocument::find( $id );
	}

	/**
	 * @param array $data
	 *
	 * @return mixed
	 */
	public function store( array $data ) {
		return $this->saveCustomerDocument( $data );
	}

	/**
	 * @param $id
	 * @param array $data
	 *
	 * @return mixed
	 */
	public function update( $id, array $data ) {
		return $this->saveCustomerDocument( $data, $id );
	}

	/**
	 * @param $id
	 *
	 * @return mixed
	 */
	public function destroy( $id ) {
		return CustomerDocument::find( $id )->delete();
	}

	/**
	 * @param $data
	 * @param null $id
	 *
	 * @return mixed
	 */
	private function saveCustomerDocument( $data, $id = null ) {
		$location = CustomerDocument::findOrNew( $id );
		$location->fill( $data );
		$location->save();

		return $location;
	}
}
