<?php


namespace App\Services\Note;


use App\Enums\NoteStatus;
use App\Enums\Roles;
use App\Models\Note;
use App\Models\Role;
use App\Services\BaseService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class NoteService extends BaseService
{
    /**
     * @param array $filters
     *
	 * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
	 */
	public function all( array $filters = [] ) {
        $query = Note::with( [ 'user.customer', 'vehicle.customer', 'vehicle.location', 'vehicle.vehicle_claims' ] );

        if ( ! empty( $filters[ 'export_id' ] ) ) {
            $query->where( 'export_id', $filters[ 'export_id' ] );
        }

        if ( ! empty( $filters[ 'vehicle_id' ] ) ) {
            $query->where( 'vehicle_id', $filters[ 'vehicle_id' ] );
        }

        if ( ! empty( $filters[ 'unread_only' ] ) ) {
            $query->where( optional( auth()->user() )->role == Roles::CUSTOMER ? 'cust_view' : 'admin_view', NoteStatus::UNREAD );
        }

        $query->whereHas( 'vehicle', function ( $q ) use ( $filters ) {
            if ( ! empty( $filters[ 'towing_request_date' ] ) ) {
                $q->whereHas( 'towing_request', function ( $q ) use ( $filters ) {
                    $q->where( DB::raw( 'LOWER(towing_request_date)' ), 'LIKE', '%' . strtolower( $filters[ 'towing_request_date' ] ) . '%' );
                } );
            }

            if ( in_array( optional( auth()->user() )->role, [ Roles::LOCATION_ADMIN, Roles::LOCATION_VIEW_ADMIN, Roles::LOCATION_RESTRICTED_ADMIN ] ) ) {
                $q->whereIn('location_id', auth()->user()->locations);

                if ( optional(auth()->user())->customers ) {
                    $q->whereHas('customer', function ( $q ) {
                        $q->whereIn('legacy_customer_id', auth()->user()->customers);
                    });
                }
            }

            if ( ! empty( $filters[ 'deliver_date' ] ) ) {
                $q->whereHas( 'towing_request', function ( $q ) use ( $filters ) {
                    $q->where( DB::raw( 'LOWER(deliver_date)' ), 'LIKE', '%' . strtolower( $filters[ 'deliver_date' ] ) . '%' );
                } );
            }

            if ( ! empty( $filters[ 'title_received_date' ] ) ) {
                $q->whereHas( 'towing_request', function ( $q ) use ( $filters ) {
                    $q->where( DB::raw( 'LOWER(title_received_date)' ), 'LIKE', '%' . strtolower( $filters[ 'title_received_date' ] ) . '%' );
                } );
            }

            if ( ! empty( $filters[ 'eta' ] ) ) {
                $q->whereHas( 'export', function ( $q ) use ( $filters ) {
                    $q->where( DB::raw( 'LOWER(eta)' ), 'LIKE', '%' . strtolower( $filters[ 'eta' ] ) . '%' );
                } );
            }

            if ( ! empty( $filters[ 'year' ] ) ) {
                $q->where( DB::raw( 'LOWER(year)' ), 'LIKE', '%' . strtolower( $filters[ 'year' ] ) . '%' );
            }

            if ( ! empty( $filters[ 'make' ] ) ) {
                $q->where( DB::raw( 'LOWER(make)' ), 'LIKE', '%' . strtolower( $filters[ 'make' ] ) . '%' );
            }

            if ( ! empty( $filters[ 'model' ] ) ) {
                $q->where( DB::raw( 'LOWER(model)' ), 'LIKE', '%' . strtolower( $filters[ 'model' ] ) . '%' );
            }

            if ( ! empty( $filters[ 'color' ] ) ) {
                $q->where( DB::raw( 'LOWER(color)' ), 'LIKE', '%' . strtolower( $filters[ 'color' ] ) . '%' );
            }

            if ( ! empty( $filters[ 'vin' ] ) ) {
                $q->where( DB::raw( 'LOWER(vin)' ), 'LIKE', '%' . strtolower( $filters[ 'vin' ] ) . '%' );
            }

            if ( ! empty( $filters[ 'lot_no' ] ) ) {
                $q->where( DB::raw( 'LOWER(lot_number)' ), 'LIKE', '%' . strtolower( $filters[ 'lot_no' ] ) . '%' );
            }

            if ( ! empty( $filters[ 'buyer_id' ] ) ) {
                $q->where( DB::raw( 'LOWER(license_number)' ), 'LIKE', '%' . strtolower( $filters[ 'buyer_id' ] ) . '%' );
            }

            if ( isset( $filters[ 'keys' ] ) ) {
                $q->where( 'keys', $filters[ 'keys' ] );
            }

            if ( isset( $filters[ 'vehicle_type' ] ) ) {
                $q->where( 'vehicle_type', $filters[ 'vehicle_type' ] );
            }

            if ( isset( $filters[ 'title' ] ) ) {
                $q->whereHas( 'towing_request', function ( $q ) use ( $filters ) {
                    $q->where( 'title_type', $filters[ 'title' ] );
                } );
            }

            if ( isset( $filters[ 'notes_status' ] ) ) {
                $q->where( 'notes_status', '=', $filters[ 'notes_status' ] );
            }

            if ( ! empty( $filters[ 'location' ] ) ) {
                $q->whereHas( 'location', function ( $q ) use ( $filters ) {
                    $q->where( 'id', $filters[ 'location' ] );
                } );
            }

            if ( ! empty( $filters[ 'status' ] ) ) {
                $q->where( 'status', $filters[ 'status' ] );
            }

            if ( isset( $filters[ 'damage_claim' ] ) ) {
                if ( $filters[ 'damage_claim' ] ) {
                    $q->whereHas( 'vehicle_claims' );
                } else {
                    $q->whereDoesntHave( 'vehicle_claims' );
                }
            }

            if ( isset( $filters[ 'claim_status' ] ) ) {
                $q->whereHas( 'vehicle_claims', function ( $query ) use ( $filters ) {
                    $query->where( 'claim_status', $filters[ 'claim_status' ] );
                } );
            }

            if ( ! empty( $filters[ 'container_no' ] ) ) {
                $q->where( DB::raw( 'LOWER(container_number)' ), 'LIKE', '%' . strtolower( $filters[ 'container_no' ] ) . '%' );
            }

            if ( isset( $filters[ 'customer_name' ] ) && (int) $filters[ 'customer_name' ] ) {
                $q->where( 'customer_user_id', $filters[ 'customer_name' ] );
            }

            if ( ! empty( $filters[ 'customer_user_id' ] ) && (int) $filters[ 'customer_user_id' ] ) {
                $q->where( 'customer_user_id', $filters[ 'customer_user_id' ] );
            }

            if ( ! empty( $filters[ 'loading_type' ] ) ) {
                $q->where( 'load_status', $filters[ 'loading_type' ] );
            }
        });

        $orderByCol = Arr::get( $filters, 'order_by_column', 'id' );

        $query->orderBy( $orderByCol, Arr::get( $filters, 'order_by', 'desc' ) );

        $limit = Arr::get( $filters, 'limit', 20 );

        return $limit != '-1' ? $query->paginate( $limit ) : $query->get();
    }

    public function vehicleNotes ( array $filters = [] )
    {
        $query = Note::with( [ 'user.customer', 'vehicle.customer', 'vehicle.location',  'vehicle.yard',  'vehicle.vehicle_claims' ] );

        if ( ! empty( $filters[ 'export_id' ] ) ) {
            $query->where( 'export_id', $filters[ 'export_id' ] );
        }

        if ( ! empty( $filters[ 'vehicle_id' ] ) ) {
            $query->where( 'vehicle_id', $filters[ 'vehicle_id' ] );
        }

        if ( ! empty( $filters[ 'unread_only' ] ) ) {
            $query->where( optional( auth()->user() )->role == Roles::CUSTOMER ? 'cust_view' : 'admin_view', NoteStatus::UNREAD );
        }

        $query->whereHas( 'vehicle', function ( $query ) use ( $filters ) {
            if ( ! empty( $filters[ 'towing_request_date' ] ) ) {
                $query->whereHas( 'towing_request', function ( $q ) use ( $filters ) {
                    $q->where( DB::raw( 'LOWER(towing_request_date)' ), 'LIKE', '%' . strtolower( $filters[ 'towing_request_date' ] ) . '%' );
                } );
            }

            if ( in_array( optional( auth()->user() )->role, [ Roles::LOCATION_ADMIN, Roles::LOCATION_VIEW_ADMIN, Roles::LOCATION_RESTRICTED_ADMIN ] ) ) {
                $query->whereIn('location_id', auth()->user()->locations);

                if ( optional(auth()->user())->customers ) {
                    $query->whereHas('customer', function ( $q ) {
                        $q->whereIn('legacy_customer_id', auth()->user()->customers);
                    });
                }
            }

            if ( ! empty( $filters[ 'deliver_date' ] ) ) {
                $query->whereHas( 'towing_request', function ( $q ) use ( $filters ) {
                    $q->where( DB::raw( 'LOWER(deliver_date)' ), 'LIKE', '%' . strtolower( $filters[ 'deliver_date' ] ) . '%' );
                } );
            }

            if ( ! empty( $filters[ 'title_received_date' ] ) ) {
                $query->whereHas( 'towing_request', function ( $q ) use ( $filters ) {
                    $q->where( DB::raw( 'LOWER(title_received_date)' ), 'LIKE', '%' . strtolower( $filters[ 'title_received_date' ] ) . '%' );
                } );
            }

            if ( ! empty( $filters[ 'eta' ] ) ) {
                $query->whereHas( 'export', function ( $q ) use ( $filters ) {
                    $q->where( DB::raw( 'LOWER(eta)' ), 'LIKE', '%' . strtolower( $filters[ 'eta' ] ) . '%' );
                } );
            }

            if ( ! empty( $filters[ 'year' ] ) ) {
                $query->where( DB::raw( 'LOWER(year)' ), 'LIKE', '%' . strtolower( $filters[ 'year' ] ) . '%' );
            }

            if ( ! empty( $filters[ 'make' ] ) ) {
                $query->where( DB::raw( 'LOWER(make)' ), 'LIKE', '%' . strtolower( $filters[ 'make' ] ) . '%' );
            }

            if ( ! empty( $filters[ 'model' ] ) ) {
                $query->where( DB::raw( 'LOWER(model)' ), 'LIKE', '%' . strtolower( $filters[ 'model' ] ) . '%' );
            }

            if ( ! empty( $filters[ 'color' ] ) ) {
                $query->where( DB::raw( 'LOWER(color)' ), 'LIKE', '%' . strtolower( $filters[ 'color' ] ) . '%' );
            }

            if ( ! empty( $filters[ 'vin' ] ) ) {
                $query->where( DB::raw( 'LOWER(vin)' ), 'LIKE', '%' . strtolower( $filters[ 'vin' ] ) . '%' );
            }

            if ( ! empty( $filters[ 'lot_no' ] ) ) {
                $query->where( DB::raw( 'LOWER(lot_number)' ), 'LIKE', '%' . strtolower( $filters[ 'lot_no' ] ) . '%' );
            }

            if ( ! empty( $filters[ 'buyer_id' ] ) ) {
                $query->where( DB::raw( 'LOWER(license_number)' ), 'LIKE', '%' . strtolower( $filters[ 'buyer_id' ] ) . '%' );
            }

            if ( isset( $filters[ 'keys' ] ) ) {
                $query->where( 'keys', $filters[ 'keys' ] );
            }

            if ( isset( $filters[ 'vehicle_type' ] ) ) {
                $query->where( 'vehicle_type', $filters[ 'vehicle_type' ] );
            }

            if ( isset( $filters[ 'title' ] ) ) {
                $query->whereHas( 'towing_request', function ( $q ) use ( $filters ) {
                    $q->where( 'title_type', $filters[ 'title' ] );
                } );
            }

            if ( isset( $filters[ 'notes_status' ] ) ) {
                $query->where( 'notes_status', '=', $filters[ 'notes_status' ] );
            }

            if ( ! empty( $filters[ 'location' ] ) ) {
                $query->whereHas( 'location', function ( $q ) use ( $filters ) {
                    $q->where( 'id', $filters[ 'location' ] );
                } );
            }

            if ( ! empty( $filters[ 'yard_id' ] ) ) {
                $query->whereHas( 'yard', function ( $q ) use ( $filters ) {
                    $q->where( 'yard_id', $filters[ 'yard_id' ] );
                } );
            }

            if ( ! empty( $filters[ 'status' ] ) ) {
                $query->where( 'status', $filters[ 'status' ] );
            }

            if ( isset( $filters[ 'damage_claim' ] ) ) {
                if ( $filters[ 'damage_claim' ] ) {
                    $query->whereHas( 'vehicle_claims' );
                } else {
                    $query->whereDoesntHave( 'vehicle_claims' );
                }
            }

            if ( isset( $filters[ 'claim_status' ] ) ) {
                $query->whereHas( 'vehicle_claims', function ( $query ) use ( $filters ) {
                    $query->where( 'claim_status', $filters[ 'claim_status' ] );
                } );
            }

            if ( ! empty( $filters[ 'container_no' ] ) ) {
                $query->where( DB::raw( 'LOWER(container_number)' ), 'LIKE', '%' . strtolower( $filters[ 'container_no' ] ) . '%' );
            }

            if ( isset( $filters[ 'customer_name' ] ) && (int) $filters[ 'customer_name' ] ) {
                $query->where( 'customer_user_id', $filters[ 'customer_name' ] );
            }

            if ( ! empty( $filters[ 'customer_user_id' ] ) && (int) $filters[ 'customer_user_id' ] ) {
                $query->where( 'customer_user_id', $filters[ 'customer_user_id' ] );
            }

            if ( ! empty( $filters[ 'loading_type' ] ) ) {
                $query->where( 'load_status', $filters[ 'loading_type' ] );
            }

            if ( ! empty( $filters[ 'vehicle_global_search' ] ) ) {
                $query->where( function ( $q ) use ( $filters ) {
                    $q->orWhere( DB::raw( 'LOWER(make)' ), 'LIKE', '%' . strtolower( $filters[ 'vehicle_global_search' ] ) . '%' )
                        ->orWhere( DB::raw( 'LOWER(model)' ), 'LIKE', '%' . strtolower( $filters[ 'vehicle_global_search' ] ) . '%' )
                        ->orWhere( DB::raw( 'LOWER(color)' ), 'LIKE', '%' . strtolower( $filters[ 'vehicle_global_search' ] ) . '%' )
                        ->orWhere( DB::raw( 'LOWER(vin)' ), 'LIKE', '%' . strtolower( $filters[ 'vehicle_global_search' ] ) . '%' )
                        ->orWhere( DB::raw( 'LOWER(lot_number)' ), 'LIKE', '%' . strtolower( $filters[ 'vehicle_global_search' ] ) . '%' )
                        ->orWhere( DB::raw( 'LOWER(container_number)' ), 'LIKE', '%' . strtolower( $filters[ 'vehicle_global_search' ] ) . '%' );
                } );
            }
        } );

        $orderByCol = Arr::get( $filters, 'order_by_column', 'id' );
        $query->orderBy( $orderByCol, Arr::get( $filters, 'order_by', 'desc' ) );


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
        return Note::with( [ 'user.customer', 'vehicle.customer', 'vehicle.location', 'vehicle.vehicle_claims' ] )->find( $id );
    }

	/**
	 * @param array $data
	 *
	 * @return mixed
	 */
	public function store( array $data ) {
		return $this->saveNote( $data );
	}

	/**
	 * @param $id
	 * @param array $data
	 *
	 * @return mixed
	 */
	public function update( $id, array $data ) {
		return $this->saveNote( $data, $id );
	}

	/**
	 * @param $id
	 *
	 * @return mixed
	 */
	public function destroy( $id ) {
		return Note::find( $id )->delete();
	}

	/**
	 * @param $data
	 * @param null $id
     *
     * @return mixed
     */
    private function saveNote ( $data, $id = null )
    {
        $note = Note::findOrNew( $id );
        $note->fill( $data );
        $note->save();

        return $note;
    }

    public function getUnreadCount ()
    {
        $query = Note::query();

        if ( optional( auth()->user() )->role == Roles::CUSTOMER ) {
            $query->whereHas( 'vehicle', function ( $q ) {
                $q->where( 'customer_user_id', auth()->user()->id );
            } );
            $query->where( 'cust_view', NoteStatus::UNREAD );
        } elseif ( in_array( optional( auth()->user() )->role, [ Roles::LOCATION_ADMIN, Roles::LOCATION_VIEW_ADMIN, Roles::LOCATION_RESTRICTED_ADMIN ] ) ) {
            $query->whereHas( 'vehicle', function ( $q ) {
                $q->whereIn( 'location_id', auth()->user()->locations );

                if( optional( auth()->user() )->customers ) {
                    $q->whereHas('customer', function ( $q ) {
                        $q->whereIn('legacy_customer_id', auth()->user()->customers );
                    });
                }
            } );
            $query->where( 'admin_view', NoteStatus::UNREAD );
        } else {
            $query->where( 'admin_view', NoteStatus::UNREAD );
        }


        return $query->whereHas('vehicle')->count();
    }

}
