<?php

namespace App\Http\Controllers\Api\V1\Invoice;

use App\Enums\ActivityType;
use App\Exports\InvoicesExport;
use App\Exports\InvoicesSummaryExport;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Presenters\InvoicePresenter;
use App\Presenters\InvoiceSummaryPresenter;
use App\Presenters\PaginatorPresenter;
use App\Services\Invoice\InvoiceService;
use App\Services\Storage\FileManager;
use App\Services\Vehicle\VehicleService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use Mockery\Exception;

class InvoicesController extends Controller
{
    private $service;

    public function __construct ( InvoiceService $service )
    {
        $this->service = $service;
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function index ( Request $request )
    {
        ini_set( 'memory_limit', '2000M' );

        if ( $request->get( 'excel-export' ) ) {
            return Excel::download( new InvoicesExport( $request->all() ), 'invoices.xlsx' );
        }

        $data = $this->service->all( array_merge( $request->all(), [ 'limit' => -1 ] ) );
        $response[ 'invoices' ] = ( new InvoicePresenter( $data->toArray() ) )->get();
        $response[ 'summary' ] = [
            'grand_total'    => number_format( $data->sum( 'total_amount' ), 2 ),
            'total_damage'   => number_format( $data->sum( 'adjustment_damaged' ), 2 ),
            'total_storage'  => number_format( $data->sum( 'adjustment_storage' ), 2 ),
            'total_discount' => number_format( $data->sum( 'adjustment_discount' ), 2 ),
            'total_other'    => number_format( $data->sum( 'adjustment_other' ), 2 ),
            'total_paid'     => number_format( $data->sum( 'paid_amount' ), 2 ),
            'total_balance'  => number_format( $data->sum( 'total_amount' ) - $data->sum( 'adjustment_damaged' ) - $data->sum( 'adjustment_storage' ) - $data->sum( 'adjustment_discount' ) - $data->sum( 'adjustment_other' ) - $data->sum( 'paid_amount' ), 2 ),
        ];

        return response()->json( $response );
    }

    public function allInvoices ( Request $request )
    {
        $data = $this->service->all( $request->all() );
        $response[ 'invoices' ] = ( new PaginatorPresenter( $data->toArray() ) )->presentBy( InvoicePresenter::class );

        return response()->json( $response );
    }

    public function summaryInvoices ( Request $request )
    {
        if ( $request->get( 'excel-export' ) ) {
            return Excel::download( new InvoicesSummaryExport( $request->all() ), 'invoices_summary.xlsx' );
        }

        $data = $this->service->summaryData( $request->all() );
        $response = ( new PaginatorPresenter( $data->toArray() ) )->presentBy( InvoiceSummaryPresenter::class );
        $response[ 'summary' ] = [
            'grand_total'    => number_format( $data->map( function ( $item ) {
                return $item->invoices->sum( 'total_amount' );
            } )->sum(), 2 ),
            'total_discount' => number_format( $data->map( function ( $item ) {
                return $item->invoices->sum( 'discount' );
            } )->sum(), 2 ),
            'total_paid'     => number_format( $data->map( function ( $item ) {
                return $item->invoices->sum( 'paid_amount' );
            } )->sum(), 2 ),
            'total_balance'  => number_format( $data->map( function ( $item ) {
                return $item->invoices->sum( 'balance' );
            } )->sum(), 2 ),
        ];

        return response()->json( $response );
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function paidInvoices ( Request $request )
    {
        if ( $request->get( 'excel-export' ) ) {
            return Excel::download( new InvoicesExport( array_merge( $request->all(), [ 'paid_only' => true ] ) ), 'paid_invoices.xlsx' );
        }

        $data = $this->service->all( array_merge( $request->all(), [ 'paid_only' => true ] ) );
        $response = ( new PaginatorPresenter( $data->toArray() ) )->presentBy( InvoicePresenter::class );
        $response[ 'summary' ] = [
            'grand_total'    => number_format( $data->sum( 'total_amount' ), 2 ),
            'total_damage'   => number_format( $data->sum( 'adjustment_damaged' ), 2 ),
            'total_storage'  => number_format( $data->sum( 'adjustment_storage' ), 2 ),
            'total_discount' => number_format( $data->sum( 'adjustment_discount' ), 2 ),
            'total_other'    => number_format( $data->sum( 'adjustment_other' ), 2 ),
            'total_paid'     => number_format( $data->sum( 'paid_amount' ), 2 ),
            'total_balance'  => number_format( $data->sum( 'total_amount' ) - $data->sum( 'adjustment_damaged' ) - $data->sum( 'adjustment_storage' ) - $data->sum( 'adjustment_discount' ) - $data->sum( 'adjustment_other' ) - $data->sum( 'paid_amount' ), 2 ),
        ];

        return response()->json( $response );
    }

    public function partiallyPaidInvoices ( Request $request )
    {
        if ( $request->get( 'excel-export' ) ) {
            return Excel::download( new InvoicesExport( array_merge( $request->all(), [ 'partially_paid_only' => true ] ) ), 'partially_paid_invoices.xlsx' );
        }

        $data = $this->service->all( array_merge( $request->all(), [ 'partially_paid_only' => true ] ) );
        $response = ( new PaginatorPresenter( $data->toArray() ) )->presentBy( InvoicePresenter::class );
        $response[ 'summary' ] = [
            'grand_total'    => number_format( $data->sum( 'total_amount' ), 2 ),
            'total_damage'   => number_format( $data->sum( 'adjustment_damaged' ), 2 ),
            'total_storage'  => number_format( $data->sum( 'adjustment_storage' ), 2 ),
            'total_discount' => number_format( $data->sum( 'adjustment_discount' ), 2 ),
            'total_other'    => number_format( $data->sum( 'adjustment_other' ), 2 ),
            'total_paid'     => number_format( $data->sum( 'paid_amount' ), 2 ),
            'total_balance'  => number_format( $data->sum( 'total_amount' ) - $data->sum( 'adjustment_damaged' ) - $data->sum( 'adjustment_storage' ) - $data->sum( 'adjustment_discount' ) - $data->sum( 'adjustment_other' ) - $data->sum( 'paid_amount' ), 2 ),
        ];

        return response()->json( $response );
    }

    public function unPaidInvoices ( Request $request )
    {
        if ( $request->get( 'excel-export' ) ) {
            return Excel::download( new InvoicesExport( array_merge( $request->all(), [ 'unpaid_only' => true ] ) ), 'unpaid_invoices.xlsx' );
        }

        $data = $this->service->all( array_merge( $request->all(), [ 'unpaid_only' => true ] ) );
        $response = ( new PaginatorPresenter( $data->toArray() ) )->presentBy( InvoicePresenter::class );
        $response[ 'summary' ] = [
            'grand_total'    => number_format( $data->sum( 'total_amount' ), 2 ),
            'total_damage'   => number_format( $data->sum( 'adjustment_damaged' ), 2 ),
            'total_storage'  => number_format( $data->sum( 'adjustment_storage' ), 2 ),
            'total_discount' => number_format( $data->sum( 'adjustment_discount' ), 2 ),
            'total_other'    => number_format( $data->sum( 'adjustment_other' ), 2 ),
            'total_paid'     => number_format( $data->sum( 'paid_amount' ), 2 ),
            'total_balance'  => number_format( $data->sum( 'total_amount' ) - $data->sum( 'adjustment_damaged' ) - $data->sum( 'adjustment_storage' ) - $data->sum( 'adjustment_discount' ) - $data->sum( 'adjustment_other' ) - $data->sum( 'paid_amount' ), 2 ),
        ];

        return response()->json( $response );
    }

    /**
     * @param Request $request
     */
    public function store ( Request $request )
    {
        $messages = [
            'required'  => 'The :attribute field is required.',
            'unique'    => 'Invoice already exists for this container'
        ];
        $this->validate( $request, [
            'customer_user_id' => 'required',
            'total_amount'     => 'required',
            'export_id'        => [
                'required',
                Rule::unique( 'invoices' )->where('customer_user_id', $request->customer_user_id)->whereNull('deleted_at'),
            ],
        ] , $messages);

        DB::beginTransaction();
        try {
            $data = $this->service->store( $request->all() );
            $logMessage = auth()->user()->username . ' has ADDED an INVOICE with export id: ' . $data->export_id;
            store_activity( $logMessage, $data->toArray(), $data->id, ActivityType::CREATE );
            DB::commit();

            debug_log( "Invoice created successfully!", $data );

            return api( $data )->success( 'Invoice Created successfully!', Response::HTTP_CREATED );
        } catch ( \Exception $e ) {
            debug_log( "Invoice create failed!", $e->getTrace() );
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

        return api( ( new InvoicePresenter( $data->toArray() ) )->get() )->success( 'Success!' );
    }

    /**
     *
     * @param $id
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update ( $id, Request $request ): \Illuminate\Http\JsonResponse
    {
        $this->validate( $request, [
            'customer_user_id' => 'required',
            'total_amount'     => 'required',
            'export_id'        => 'required',
        ] );

        DB::beginTransaction();
        try {
            if( $request->version_id ) {
                $invoice = Invoice::findOrFail( $id );
                if( $invoice->version_id > $request->version_id ) {
                    return api()->fails( 'Data Already changed form another device, please refresh the page and try again.', Response::HTTP_BAD_REQUEST );
                }
            }

            $data = $this->service->update( $id, $request->all() );
            $logMessage = auth()->user()->username . ' has UPDATE an INVOICE with export id: ' . $data->export_id;
            store_activity( $logMessage, $data->getChanges(), $data->id, ActivityType::UPDATE );
            DB::commit();

            debug_log( "Invoice updated successfully!", $data );

            return api( $data )->success( 'Invoice Updated successfully!', Response::HTTP_CREATED );
        } catch ( \Exception $e ) {
            debug_log( "Invoice update failed!", $e->getTrace() );
            DB::rollback();

            return api()->fails( $e->getMessage(), Response::HTTP_BAD_REQUEST );
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
            $invoice = Invoice::find( $id );
            $data = $this->service->destroy( $id );
            $logMessage = auth()->user()->username . ' has DELETED an INVOICE with export id: ' . $invoice->export_id;
            store_activity( $logMessage, $invoice->toArray(), $id, ActivityType::DELETE );

            debug_log( "Invoice deleted successfully!", $data );

            return api( $data )->success( 'Invoice Deleted Successfully!' );
        } catch ( Exception $e ) {
            debug_log( "Invoice deletion failed!", $e->getTrace() );

            return api()->fails( $e->getMessage(), Response::HTTP_BAD_REQUEST );
        }
    }

    public function overview ( Request $request )
    {
        $data = $this->service->invoiceSummary( $request->all() );
        $response = [
            [ 'label' => 'Unpaid Amount', 'amount' => Arr::get( $data, 'unpaid' ) ],
            [ 'label' => 'Paid Amount', 'amount' => Arr::get( $data, 'paid' ) ],
            [ 'label' => 'Partial Amount', 'amount' => Arr::get( $data, 'partially' ) ],
            [ 'label' => 'Total Amount', 'amount' => Arr::get( $data, 'total' ) ],
            [ 'label' => 'Total Discount', 'amount' => Arr::get( $data, 'discount' ) ],
            [ 'label' => 'Worth', 'amount' => app( VehicleService::class )->getWorthAmount( $request->all() ) ],
        ];

        return api( $response )->success( 'Success!' );
    }

    public function xmlUpload ( Request $request )
    {
        $this->validate($request, [
            'file' => 'required|file',
        ]);
        try {
            $xmlString = $request->file->getContent();
            $pattern = "/<SUM_INVOICE>(.+?)<\/SUM_INVOICE>/";
            $amount = '';
            if ( preg_match( $pattern, $xmlString, $match ) ) {
                $amount = $match[ 1 ];
            }

            return api( [ 'amount' => $amount ] )->success( 'Success!', Response::HTTP_CREATED );
        } catch ( Exception $e ) {
            return api()->fails( $e->getMessage(), Response::HTTP_BAD_REQUEST );
        }

    }

    /**
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function graphicalNotation ( $customerUserId ): \Illuminate\Http\JsonResponse
    {
        return response()->json( $this->service->graphicalNotation( $customerUserId ) );
    }

    /**
     * @param $customerUserId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function monthlyGraph ( $customerUserId ): \Illuminate\Http\JsonResponse
    {
        return response()->json( $this->service->monthlyGraph( $customerUserId ) );
    }

    public function uploadDocument ( Request $request )
    {
        $upload = app( FileManager::class )->upload( $request->file, 'uploads/invoices' );
        if ( ! $upload ) {
            return response()->json( ['success' => 'false', 'data' => 'Failed to file upload'], Response::HTTP_UNPROCESSABLE_ENTITY );
        }

        return response()->json( ['success' => 'true', 'data' => $upload] );
    }

    public function exportExcel ( $customerUserId, Request $request)
    {
        ini_set( 'memory_limit', '2000M' );
        set_time_limit( 0 );

        $filters = $request->all();
        $filters['customer_user_id'] = $customerUserId;

        return Excel::download( new InvoicesExport( $filters ), 'invoices.xlsx' );
    }

}
