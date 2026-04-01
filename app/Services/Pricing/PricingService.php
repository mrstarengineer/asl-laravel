<?php


namespace App\Services\Pricing;


use App\Models\Pricing;
use App\Services\BaseService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PricingService extends BaseService {

	/**
	 * @param array $filters
	 *
	 * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
	 */
	public function all( array $filters = [] ) {
		$query = Pricing::query();

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

		if (!empty($filters['pricing_global_search'])) {
			$query->where(function ($q) use ($filters) {
				$q->where('status', $filters['pricing_global_search'])
				  ->orWhere('pricing_type', $filters['pricing_global_search'])
				  ->orWhere(DB::raw('LOWER(month)'), 'LIKE', '%' . strtolower($filters['pricing_global_search']) . '%');
			});
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
    public function getById ( $id )
    {
        return Pricing::find( $id );
    }

    /**
     * @param array $data
     *
     * @return mixed
     */
    public function store ( array $data )
    {
        if ( ! empty( $data[ 'upload_file' ] ) ) {
//            $path = 'uploads/pricing/' . time() . str_replace( ' ', '_', trans( 'pricing.pricing_type.' . $data[ 'pricing_type' ] ) ) . '-' . date( 'M-Y', strtotime( $data[ 'month' ] ) ) . '.' . pathinfo( $data[ 'upload_file' ], PATHINFO_EXTENSION );
//            if ( Storage::exists( $path ) ) {
//                Storage::delete($path);
//            }
//            Storage::move( str_replace( env( 'AWS_S3_BASE_URL' ), '', $data[ 'upload_file' ] ), $path );
            $data[ 'upload_file' ] = str_replace( env( 'AWS_S3_BASE_URL' ), '', $data[ 'upload_file' ] );
        }

        return $this->savePricing( $data );
    }

    /**
     * @param $id
     * @param array $data
     *
     * @return mixed
     */
    public function update ( $id, array $data )
    {
        return $this->savePricing( $data, $id );
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function destroy ( $id )
    {
        return Pricing::find( $id )->delete();
    }

    /**
     * @param $data
     * @param null $id
     *
     * @return mixed
     */
    private function savePricing ( $data, $id = null )
    {
        $data[ 'str_month' ] = $data[ 'month' ];
        $data[ 'month' ] = date( 'M-Y', strtotime( $data[ 'month' ] ) );
        $pricing = Pricing::findOrNew( $id );
        $pricing->fill( $data );
        $pricing->upload_file = str_replace( env( 'AWS_S3_BASE_URL' ), '', $pricing->upload_file );
        $pricing->save();

        return $pricing;
    }
}
