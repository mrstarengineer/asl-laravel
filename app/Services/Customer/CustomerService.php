<?php


namespace App\Services\Customer;


use App\Enums\Roles;
use App\Enums\VehicleStatus;
use App\Models\Consignee;
use App\Models\Customer;
use App\Models\CustomerDocument;
use App\Models\Export;
use App\Models\User;
use App\Services\BaseService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class CustomerService extends BaseService
{

    /**
     * @param array $filters
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function all( array $filters = [] )
    {
        $query = Customer::withCount( [
            'exports'  => function ( $q ) {
                $q->where( 'status', '<>', VehicleStatus::ARRIVED );
            },
            'vehicles' => function ( $q ) {
                $q->whereIn( 'status', [ VehicleStatus::ON_HAND, VehicleStatus::ON_THE_WAY, VehicleStatus::MANIFEST, VehicleStatus::SHIPPED ] );
            },
        ] );

        if ( isset( $filters[ 'select' ] ) ) {
            $query->select( $filters[ 'select' ] );
        } else {
            $query->with( [ 'user' ] );
        }

        if ( !empty( $filters[ 'user_id' ] ) ) {
            $query->where( 'user_id', $filters[ 'user_id' ] );
        }

        if ( optional( auth()->user() )->role == Roles::CUSTOMER ) {
            $query->where( 'user_id', auth()->user()->id );
        }

        if ( isset( $filters[ 'status' ] ) ) {
            $query->whereHas( 'user', function ( $q ) use ( $filters ) {
                $q->where( 'users.status', $filters[ 'status' ] );
            } );
        }

        if ( optional( auth()->user() )->role == Roles::LOCATION_RESTRICTED_ADMIN ) {
            $query->whereIn( 'legacy_customer_id', auth()->user()->customers );
        }

        if ( !empty( $filters[ 'customer_name' ] ) ) {
            $query->where( function ( $q ) use ( $filters ) {
                $q->where( DB::raw( 'LOWER(customer_name)' ), 'LIKE', '%' . strtolower( $filters[ 'customer_name' ] ) . '%' )
                    ->orWhere( DB::raw( 'LOWER(company_name)' ), 'LIKE', '%' . strtolower( $filters[ 'customer_name' ] ) . '%' )
                    ->orWhere( 'legacy_customer_id', $filters[ 'customer_name' ] );
            } );
        }

        if ( !empty( $filters[ 'company_name' ] ) ) {
            $query->where( DB::raw( 'LOWER(company_name)' ), 'LIKE', '%' . strtolower( $filters[ 'company_name' ] ) . '%' );
        }

        if ( !empty( $filters[ 'phone' ] ) ) {
            $query->where( DB::raw( 'LOWER(phone)' ), 'LIKE', '%' . strtolower( $filters[ 'phone' ] ) . '%' );
        }

        if ( !empty( $filters[ 'phone_two' ] ) ) {
            $query->where( DB::raw( 'LOWER(phone_two)' ), 'LIKE', '%' . strtolower( $filters[ 'phone_two' ] ) . '%' );
        }

        if ( !empty( $filters[ 'loading_type' ] ) ) {
            $query->where( 'loading_type', $filters[ 'loading_type' ] );
        }

        if ( !empty( $filters[ 'customer_global_search' ] ) ) {
            $query->where( function ( $q ) use ( $filters ) {
                $q->where( 'legacy_customer_id', $filters[ 'customer_global_search' ] )
                    ->orWhere( DB::raw( 'LOWER(customer_name)' ), 'LIKE', '%' . strtolower( $filters[ 'customer_global_search' ] ) . '%' )
                    ->orWhere( DB::raw( 'LOWER(company_name)' ), 'LIKE', '%' . strtolower( $filters[ 'customer_global_search' ] ) . '%' )
                    ->orWhere( DB::raw( 'LOWER(phone)' ), 'LIKE', '%' . strtolower( $filters[ 'customer_global_search' ] ) . '%' )
                    ->orWhereHas( 'user', function ( $query ) use ( $filters ) {
                        $query->where( DB::raw( 'LOWER(email)' ), 'LIKE', '%' . strtolower( $filters[ 'customer_global_search' ] ) . '%' );
                    } );
            } );
        }

        if(auth()->user()->role == 2) {
            if( ! in_array(16, optional(auth()->user())->locations ?? [] ) ) {
                $query->whereNotIn('user_id', explode(",", env('CHINA_CUSTOMER_USER_IDS')));
            }
        }else if(  ! in_array(auth()->user()->role,  [0]  ) ) {
            $query->whereNotIn('user_id', explode(",", env('CHINA_CUSTOMER_USER_IDS')));
        }

        $query->orderBy( Arr::get( $filters, 'order_by_column', 'customer_name' ), Arr::get( $filters, 'order_by', 'ASC' ) );
        $limit = Arr::get( $filters, 'limit', 20 );

        if ( $limit != -1 ) {
            return $query->paginate( $limit );
        }

        return Arr::get( $filters, 'query_only', false ) ? $query : $query->get();
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function getById( $id )
    {
        if ( optional( auth()->user() )->role == Roles::CUSTOMER ) {
            $id = Customer::where('user_id', auth()->user()->id)->first()->id;
        }

        // return Customer::with( [ 'user', 'customer_documents', 'consignees', 'country', 'state', 'city' ] )->withCount( [ 'exports', 'vehicles' ] )->find( $id );
        return Customer::with(['user', 'customer_documents', 'consignees', 'country', 'state', 'city'])
            ->withCount(['vehicles'])
            ->addSelect([
                'exports_count' => Export::select(DB::raw('count(DISTINCT exports.id)'))
                    ->join('vehicles', 'vehicles.export_id', '=', 'exports.id')
                    ->whereColumn('vehicles.customer_user_id', 'customers.user_id')
                    ->whereNull('exports.deleted_at')
                    ->whereNull('vehicles.deleted_at')
            ])
            ->find($id);
    }

    public function getCustomerByUserId( $id )
    {
        return Customer::where( 'user_id', $id )->firstOrFail();
    }

    /**
     * @param array $data
     *
     * @return mixed
     */
    public function store( array $data )
    {
        return $this->saveCustomer( $data );
    }

    /**
     * @param $id
     * @param array $data
     *
     * @return mixed
     */
    public function update( $id, array $data )
    {
        return $this->saveCustomer( $data, $id );
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function destroy( $id )
    {
        return Customer::find( $id )->delete();
    }

    /**
     * @param $data
     * @param null $id
     *
     * @return mixed
     */
    private function saveCustomer( $data, $id = null )
    {
        unset( $data[ 'version_id' ] );

        $data[ 'customer_name' ] = trim( $data[ 'customer_name' ] );
        $data[ 'company_name' ] = trim( $data[ 'company_name' ] );
        $customerDetail = $this->getById( $id );
        if ( !empty( $customerDetail ) ) {
            $user = User::findOrNew( $customerDetail->user_id );
        } else {
            $user = new User();
        }
        $user->username = $data[ 'username' ];
        $user->email = $data[ 'email' ];
        $user->role = Roles::CUSTOMER;
        if ( !empty( $data[ 'password' ] ) ) {
            $user->password = Hash::make( $data[ 'password' ] );
        }
        if ( !empty( $data[ 'photo_url' ] ) ) {
            $user->photo_url = str_replace( env( 'AWS_S3_BASE_URL' ), '', $data[ 'photo_url' ] );
        }
        $user->save();

        if ( $id == null ) {
            $consignee = new Consignee();
            $consignee->customer_user_id = $user->id;
            $consignee->consignee_name = $data[ 'company_name' ];
            $consignee->consignee_address_1 = $data[ 'address_line_1' ];
            $consignee->consignee_address_2 = $data[ 'address_line_2' ];
            $consignee->city_id = $data[ 'city_id' ];
            $consignee->state_id = $data[ 'state_id' ];
            $consignee->country_id = $data[ 'country_id' ];
            $consignee->zip_code = $data[ 'zip_code' ];
            $consignee->phone = $data[ 'phone' ];
            $consignee->save();

            $data[ 'user_id' ] = $user->id;
        }

        $this->deleteCustomerDocuments( Arr::get( $data, 'customer_documents', [] ), $user->id );
        if ( Arr::get( $data, 'fileUrls' ) ) {
            $this->createNewDocuments( $data[ 'fileUrls' ], $user->id );
        }

        $customer = Customer::findOrNew( $id );
        $customer->fill( $data );
        $customer->save();

        return $customer;
    }

    public function createNewDocuments( $files, $userId )
    {
        foreach ( $files as $url ) {
            $url = str_replace( env( 'AWS_S3_BASE_URL' ), '', $url );
            if ( !Storage::exists( 'uploads/customers/documents/' . $userId ) ) {
                Storage::makeDirectory( 'uploads/customers/documents/' . $userId );
            }
            /*if( $url && $url !== 'uploads/customers/documents/' . $userId . '/' . basename( $url ) ) {
                Storage::move( $url, 'uploads/customers/documents/' . $userId . '/' . basename( $url ) );
            }*/

            CustomerDocument::create( [
                'customer_user_id' => $userId,
                //                'file'             => 'uploads/customers/documents/' . $userId . '/' . basename( $url ),
                'file'             => $url,
            ] );
        }
    }

    private function deleteCustomerDocuments( $documents, $userId )
    {
        $deletableDocuments = CustomerDocument::where( [
            'customer_user_id' => $userId,
        ] )->whereNotIn( 'id', collect( $documents )->pluck( 'id' )->toArray() )->get();
//        foreach ( $deletableDocuments as $image ) {
//            Storage::delete( $image->file );
//        }
        CustomerDocument::whereIn( 'id', $deletableDocuments->pluck( 'id' )->toArray() )->delete();
    }

    public function vehicleLoading( $filters = [] )
    {
        $query = Customer::query()
            ->select( [
                'customers.customer_name AS customer_name', 'customers.legacy_customer_id', 'customers.id', 'vehicles.customer_user_id AS customer_user_id',
                DB::raw( 'SUM( IF(vehicles.status = 1, 1, 0) ) AS on_hand' ),
                DB::raw( 'SUM( IF(vehicles.status = 1, 1, 0)  AND IF(towing_requests.title_type = 1, 1, 0)) AS exportable' ),
                DB::raw( 'SUM( IF(vehicles.status = 1, 1, 0)  AND IF(towing_requests.title_type = 0, 1, 0)) AS no_title' ),
                DB::raw( 'SUM( IF(vehicles.status = 3, 1, 0) ) AS on_the_way' ),
                DB::raw( 'SUM( IF(vehicles.status = 1, 1, 0) AND IF(towing_requests.title_type = 2, 1, 0) ) AS pending' ),
                DB::raw( 'SUM( IF(vehicles.status = 1, 1, 0) AND IF(towing_requests.title_type = 3, 1, 0)) AS bos' ),
                DB::raw( 'SUM( IF(vehicles.status = 1, 1, 0) AND IF(towing_requests.title_type = 4, 1, 0)) AS lien' ),
                DB::raw( 'SUM( IF(vehicles.status = 1, 1, 0) AND IF(towing_requests.title_type = 6, 1, 0)) AS rejected' ),
                DB::raw( 'SUM( IF(vehicles.status = 1, 1, 0) AND IF(towing_requests.title_type = 5, 1, 0)) AS mv907' ),
            ] )
            ->join( 'vehicles', 'vehicles.customer_user_id', '=', 'customers.user_id' )
            ->join( 'towing_requests', 'vehicles.towing_request_id', '=', 'towing_requests.id' )
            ->groupBy( 'customers.legacy_customer_id', 'customers.id', 'customers.customer_name', 'vehicles.customer_user_id' );

        if ( !empty( $filters[ 'vehicle_global_search' ] ) ) {
            $query->where( function ( $q ) use ( $filters ) {
                $q->where( 'customers.legacy_customer_id', $filters[ 'vehicle_global_search' ] )
                    ->orWhere( DB::raw( 'LOWER(customer_name)' ), 'LIKE', '%' . strtolower( $filters[ 'vehicle_global_search' ] ) . '%' )
                    ->orWhere( DB::raw( 'LOWER(company_name)' ), 'LIKE', '%' . strtolower( $filters[ 'vehicle_global_search' ] ) . '%' )
                    ->orWhere( DB::raw( 'LOWER(phone)' ), 'LIKE', '%' . strtolower( $filters[ 'vehicle_global_search' ] ) . '%' )
                    ->orWhereHas( 'user', function ( $query ) use ( $filters ) {
                        $query->where( DB::raw( 'LOWER(email)' ), 'LIKE', '%' . strtolower( $filters[ 'vehicle_global_search' ] ) . '%' );
                    } );
            } );
        }

        if ( in_array( optional( auth()->user() )->role, [ Roles::LOCATION_ADMIN, Roles::LOCATION_VIEW_ADMIN ] ) ) {
            $query->whereIn( 'vehicles.location_id', auth()->user()->locations );
        } elseif ( optional( auth()->user() )->role == Roles::CUSTOMER ) {
            $query->where( 'vehicles.customer_user_id', auth()->user()->id );
        }

        $query->orderBy( Arr::get( $filters, 'order_by_column', 'customers.customer_name' ), Arr::get( $filters, 'order_by', 'ASC' ) );

        $limit = Arr::get( $filters, 'limit', 20 );

        return $limit != '-1' ? $query->paginate( $limit ) : $query->get();
    }
}
