<?php

namespace App\Http\Controllers\Api\V1\Customer;

use App\Enums\ActivityType;
use App\Enums\Roles;
use App\Exports\CustomersExport;
use App\Exports\VehicleLoadingExport;
use App\Http\Controllers\Controller;
use App\Jobs\SyncSingleUserPermissionsJob;
use App\Models\Customer;
use App\Models\CustomerDocument;
use App\Presenters\CustomerDetailPresenter;
use App\Presenters\LoadingStatusPresenter;
use App\Presenters\PaginatorPresenter;
use App\Presenters\CustomerPresenter;
use App\Services\Customer\CustomerService;
use App\Services\Storage\FileManager;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use League\Flysystem\Filesystem;
use League\Flysystem\ZipArchive\ZipArchiveAdapter;
use Maatwebsite\Excel\Facades\Excel;
use Mockery\Exception;

class CustomerController extends Controller
{
    private $service;

    public function __construct ( CustomerService $service )
    {
        $this->service = $service;
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index ( Request $request ): \Illuminate\Http\JsonResponse
    {
        $data = $this->service->all( $request->all() )->toArray();
        $data = ( new PaginatorPresenter( $data ) )->presentBy( CustomerPresenter::class );

        return response()->json( $data );
    }

    public function customerList ( Request $request ): \Illuminate\Http\JsonResponse
    {
        $customerList = app( CustomerService::class )->all(
            array_merge( $request->all(), [ 'select' => [ 'user_id', 'customer_name', 'company_name', 'legacy_customer_id' ] ] )
        )->toArray();

        return response()->json( $customerList );
    }

    /**
     * @param Request $request
     */
    public function store ( Request $request )
    {
        $rules = [
            'email'              => 'required|email|unique:App\Models\User',
            'legacy_customer_id' => 'required|unique:App\Models\Customer',
            'username'           => 'required|unique:App\Models\User',
            'password'           => 'required',
            'customer_name'      => 'required',
            'company_name'       => 'required',
//            'phone'              => 'required',
            'address_line_1'     => 'required',
            'country_id'         => 'required',
            'state_id'           => 'required',
            'city_id'            => 'required',
        ];

        $this->validate( $request, $rules );

        DB::beginTransaction();
        try {
            $data = $this->service->store( $request->all() );
            $logMessage = auth()->user()->username . ' has ADDED a CUSTOMER with username: ' . $request->username;
            store_activity( $logMessage, $request->all(), $data->id, ActivityType::CREATE );
            DB::commit();

            SyncSingleUserPermissionsJob::dispatch( Roles::CUSTOMER + 1, $data->user_id );

            debug_log( "Customer created successfully!", $data );

            return api( $data )->success( 'Customer Created successfully!', Response::HTTP_CREATED );
        } catch ( \Exception $e ) {
            debug_log( "Customer create failed!", $e->getTrace() );
            DB::rollback();

            return api()->fails( $e->getMessage(), Response::HTTP_BAD_REQUEST );
        }
    }

    /**
     *
     * @param $id
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show ( $id, Request $request ): \Illuminate\Http\JsonResponse
    {
        $data = $this->service->getById( $id );
        $data = $data ? ( new CustomerDetailPresenter( $data->toArray() ) )->get() : [];

        return api( $data )->success( 'Success!' );
    }

    /**
     *
     * @param $id
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update ( $id, Request $request )
    {
        $customer = $this->service->getById( $id );
        $rules = [
            'email'          => [
                'required',
                'email',
                Rule::unique( 'users' )->ignore( $customer->user_id )->whereNull('deleted_at'),
            ],
            'legacy_customer_id'       => [
                'required',
                Rule::unique( 'customers' )->ignore( $customer->id )->whereNull('deleted_at'),
            ],
            'username'       => [
                'required',
                Rule::unique( 'users' )->ignore( $customer->user_id )->whereNull('deleted_at'),
            ],
            'customer_name'  => 'required',
            'company_name'   => 'required',
//            'phone'          => 'required',
            'address_line_1' => 'required',
            'country_id'     => 'required',
            'state_id'       => 'required',
            'city_id'        => 'required',
        ];
        $this->validate( $request, $rules );

        DB::beginTransaction();
        try {
            if( $request->version_id ) {
                $customer = Customer::findOrFail( $id );
                if( $customer->version_id > $request->version_id ) {
                    return api()->fails( 'Data Already changed form another device, please refresh the page and try again.', Response::HTTP_BAD_REQUEST );
                }
            }

            $data = $this->service->update( $id, $request->all() );
            $logMessage = auth()->user()->username . ' has UPDATED a CUSTOMER with username: ' . $request->username;
            store_activity( $logMessage, $customer->getChanges(), $data->id, ActivityType::UPDATE );
            DB::commit();

            debug_log( "Customer updated successfully!", $data );

            return api( $data )->success( 'Customer Updated successfully!', Response::HTTP_CREATED );
        } catch ( \Exception $e ) {
            debug_log( "Customer update failed!", $e->getTrace() );
            DB::rollback();

            return api()->fails( $e->getTraceAsString(), Response::HTTP_BAD_REQUEST );
        }
    }

    /**
     *
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy ( $id ): \Illuminate\Http\JsonResponse
    {
        try {
            $customer = Customer::find( $id )->with('user');
            $data = $this->service->destroy( $id );
            $logMessage = optional( auth()->user() )->username . ' has been DELETED a CUSTOMER with username: ' . optional( $customer->user )->username;
            store_activity( $logMessage, $customer->toArray(), $id, ActivityType::DELETE );
            debug_log( "Customer deleted successfully!", $data );

            return api( $data )->success( 'Customer Deleted Successfully!' );
        } catch ( Exception $e ) {
            debug_log( "Customer deletion failed!", $e->getTrace() );

            return api()->fails( $e->getMessage(), Response::HTTP_BAD_REQUEST );
        }
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportExcel ( Request $request )
    {
        return Excel::download( new CustomersExport( $request->all() ), 'customers.xlsx' );
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportPdf ( Request $request )
    {
        return (new CustomersExport)->download('customers.pdf', \Maatwebsite\Excel\Excel::DOMPDF);
//        return Excel::download( new CustomersExport( $request->all() ), 'customers.pdf', \Maatwebsite\Excel\Excel::DOMPDF );
    }

    public function nextCustomerId ()
    {
        return response()->json( [ 'success' => 'true', 'legacy_customer_id' => ( Customer::where('legacy_customer_id', '>', 202000)->where('legacy_customer_id', '<', 213000)->max('legacy_customer_id') + 1 ) ] );
    }

    public function uploadProfilePhoto ( $id, Request $request )
    {
        $upload = app( FileManager::class )->upload( $request->file, 'uploads/users/' . $id );
        if ( ! $upload ) {
            return response()->json( [ 'success' => 'false', 'message' => 'Failed to file upload' ], 400 );
        }

        return response()->json( [ 'success' => 'true', 'data' => $upload ] );
    }

    /**
     * @param $id
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadDocuments ( $id ): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        try {
            $customer = $this->service->getById( $id );
            $allDocuments = CustomerDocument::where( [ 'customer_user_id' => $customer->user_id ] )->get();

            if ( $allDocuments->count() == 0 ) {
                throw new \Exception( 'No Documents Found' );
            }

            $file = str_replace( ' ', '_', strtolower( $customer->customer_name ) ) . '_documents' . '.zip';
            $zipFileUrl = public_path( 'uploads/' . $file );

            if ( file_exists( $zipFileUrl ) ) {
                unlink( $zipFileUrl );
            }

            $zip = new Filesystem(new ZipArchiveAdapter($zipFileUrl));

            foreach ( $allDocuments as $files ) {
                $zip->put( basename( $files->file ), file_get_contents( Storage::url( $files->file ) ) );
            }
            $zip->getAdapter()->getArchive()->close();

            if ( file_exists( $zipFileUrl ) ) {
                return response()->download( $zipFileUrl )->deleteFileAfterSend( true );
            }

            throw new \Exception( 'Something went wrong.' );
        } catch ( \Exception $e ) {
            throw new \Exception( $e->getMessage() );
        }
    }

    public function vehicleLoading ( Request $request )
    {
        $data = $this->service->vehicleLoading( $request->all() )->toArray();
        $data = ( new PaginatorPresenter( $data ) )->presentBy( LoadingStatusPresenter::class );

        return response()->json( $data );
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function vehicleLoadingExport ( Request $request )
    {
        return Excel::download( new VehicleLoadingExport( $request->all() ), 'loading_of_vehicles.xlsx' );
    }

    public function customerDocumentAdd ( $id, Request $request )
    {
        try {
            $customer = $this->service->getById( $id );
            if( ! Storage::exists('uploads/customers/documents/' . $customer->user_id) ) {
                Storage::makeDirectory('uploads/customers/documents/' . $customer->user_id );
            }
            foreach ( $request->fileUrls as $url ) {
                if( str_replace( env( 'AWS_S3_BASE_URL' ), '', $url ) !== 'uploads/customers/documents/' . $customer->user_id . '/' . basename( $url ) ) {
                    Storage::move( str_replace( env( 'AWS_S3_BASE_URL' ), '', $url ), 'uploads/customers/documents/' . $customer->user_id . '/' . basename( $url ) );
                }
                if ( filter_var($url, FILTER_VALIDATE_URL) ) {
                    CustomerDocument::create( [
                        'customer_user_id' => $customer->user_id,
                        'file'             => 'uploads/customers/documents/' . $customer->user_id . '/' . basename( $url ),
                    ] );
                }
            }

            debug_log( "Customer documents upload successfully!" );

            return response()->json( ['responseCode' => 1, 'data' => 'Successfully upload documents'] );
        } catch ( Exception $e ) {
            return response()->json( ['responseCode' => 0, 'data' => $e->getMessage()], 400 );
        }
    }
}


