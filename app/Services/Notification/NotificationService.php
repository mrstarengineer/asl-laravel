<?php


namespace App\Services\Notification;


use App\Models\Notification;
use App\Services\BaseService;
use Illuminate\Support\Arr;

class NotificationService extends BaseService {

	/**
	 * @param array $filters
	 *
	 * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
	 */
	public function all( array $filters = [] ) {
		$query = Notification::query();

		if ( ! empty( $filters['status'] ) ) {
			$query->where( 'status', $filters['status'] );
		}

        if ( ! empty( $filters['expire_date'] ) ) {
            $query->where( 'expire_date', $filters['expire_date'] );
        }

		$limit = Arr::get( $filters, 'limit', 20 );
        $query->orderBy( Arr::get( $filters, 'order_by_column', 'id' ), Arr::get( $filters, 'order_by', 'desc' ) );

		return $limit != '-1' ? $query->paginate( $limit ) : $query->get();
	}

	/**
	 * @param $id
	 *
	 * @return mixed
	 */
	public function getById( $id ) {
		return Notification::find( $id );
	}

	/**
     * @param array $data
     *
     * @return mixed
     */
    public function store ( array $data )
    {
        return $this->saveNotification( $data );
    }

    /**
     * @param $id
     * @param array $data
     *
     * @return mixed
     */
    public function update ( $id, array $data )
    {
        return $this->saveNotification( $data, $id );
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function destroy ( $id )
    {
        return Notification::find( $id )->delete();
    }

    /**
     * @param $data
     * @param null $id
     *
     * @return mixed
     */
    private function saveNotification ( $data, $id = null )
    {
        $location = Notification::findOrNew( $id );
        $location->fill( $data );
        $location->save();

        return $location;
    }

    /**
     * Get non expired notification count
     *
     * @return mixed
     */
    public function nonExpiredNotificationCount ()
    {
        return Notification::where( 'expire_date', '>', date( 'Y-m-d' ) )->count();
    }
}
