<?php


namespace App\Services\Invoice;


use App\Enums\Roles;
use App\Enums\VehicleDocumentType;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\VehicleDocument;
use App\Services\BaseService;
use App\Services\Customer\CustomerService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class InvoiceService extends BaseService
{
    public function all ( array $filters = [] )
    {
        $query = Invoice::query()->with(['customer', 'export', 'documents']);

        if ( ! empty( $filters[ 'export_id' ] ) ) {
            $query->where( 'export_id', $filters[ 'export_id' ] );
        }

        if ( optional(auth()->user())->role == Roles::CUSTOMER ) {
            $query->where( 'customer_user_id', auth()->user()->id );
        } elseif ( ! empty( $filters[ 'customer_user_id' ] ) ) {
            $query->where( 'customer_user_id', $filters[ 'customer_user_id' ] );
        }

        if ( ! empty( $filters[ 'consignee_id' ] ) ) {
            $query->where( 'consignee_id', $filters[ 'consignee_id' ] );
        }

        if ( ! empty( $filters[ 'seen_by_customer' ] ) ) {
            $query->where( 'seen_by_customer', $filters[ 'seen_by_customer' ] );
        }

        if ( Arr::get( $filters, 'paid_only' ) ) {
            $query->whereRaw( "paid_amount is not null and paid_amount!=0 and paid_amount <> '' " );
        } elseif ( Arr::get( $filters, 'unpaid_only' ) ) {
            $query->where( function ( $q ) {
                $q->whereNull( 'paid_amount' )
                    ->orWhere( 'paid_amount', 0 )
                    ->orWhere( 'paid_amount', '' );
            } );
        } elseif ( Arr::get( $filters, 'partially_paid_only' ) ) {
            $query->whereRaw( "paid_amount is not null and paid_amount!=0 and paid_amount <> '' " );
            $query->whereRaw( 'total_amount>(paid_amount+adjustment_damaged+adjustment_storage+adjustment_discount+adjustment_other)' );
        }

        if ( ! empty( $filters[ 'invoice_global_search' ] ) ) {
            $query->where( function ( $q ) use ( $filters ) {
                $q->whereHas( 'export', function ( $q ) use ( $filters ) {
                    $q->where( DB::raw( 'LOWER(container_number)' ), 'LIKE', '%' . strtolower( $filters[ 'invoice_global_search' ] ) . '%' )
                        ->orWhere( DB::raw( 'LOWER(ar_number)' ), 'LIKE', '%' . strtolower( $filters[ 'invoice_global_search' ] ) . '%' );

                } );
                $q->orWhereHas( 'customer', function ( $q ) use ( $filters ) {
                    $q->where( DB::raw( 'LOWER(customer_name)' ), 'LIKE', '%' . strtolower( $filters[ 'invoice_global_search' ] ) . '%' )
                        ->orWhere( 'legacy_customer_id', '=', $filters[ 'invoice_global_search' ] );
                } );
            } );
        }

        $limit = Arr::get( $filters, 'limit', 20 );
        $query->orderBy('id', 'DESC');

        return $limit != '-1' ? $query->paginate( $limit ) : $query->get();
    }

    public function summaryData ( array $filters = [] )
    {
        $query = Customer::with( [ 'invoices' ] );

        if ( optional(auth()->user())->role == Roles::CUSTOMER ) {
            $query->where( 'user_id', auth()->user()->id );
        }

        if ( ! empty( $filters[ 'customer_global_search' ] ) ) {
            $query->where( function ( $q ) use ( $filters ) {
                $q->where( 'legacy_customer_id', $filters[ 'customer_global_search' ] )
                    ->orWhere( DB::raw( 'LOWER(customer_name)' ), 'LIKE', '%' . strtolower( $filters[ 'customer_global_search' ] ) . '%' )
                    ->orWhere( DB::raw( 'LOWER(company_name)' ), 'LIKE', '%' . strtolower( $filters[ 'customer_global_search' ] ) . '%' )
                    ->orWhereHas('invoices.export', function ( $q ) use ( $filters) {
                        $q->where('container_number', $filters[ 'customer_global_search' ]);
                    });
            } );
        }

        $limit = Arr::get( $filters, 'limit', 20 );
        $data = $limit != '-1' ? $query->paginate( $limit ) : $query->get();

        return $data;
    }

    public function getById ( $id )
    {
        return Invoice::with(['customer', 'export', 'documents'])->find( $id );
    }

    public function store ( array $data )
    {
        return $this->saveInvoice( $data );
    }

    public function update ( $id, array $data )
    {
        $this->removeInvoiceDocuments( $id, $data['documents'] );

        return $this->saveInvoice( $data, $id );
    }

    public function destroy ( $id )
    {
        return Invoice::find( $id )->delete();
    }

    private function saveInvoice ( $data, $id = null )
    {
        unset( $data[ 'version_id' ] );

        if( isset( $data['clearance_invoice']) ) {
            $data['clearance_invoice'] = str_replace( env( 'AWS_S3_BASE_URL' ), '', $data['clearance_invoice'] );
        }
        if( isset( $data['upload_invoice']) ) {
            $data['upload_invoice'] = str_replace( env( 'AWS_S3_BASE_URL' ), '', $data['upload_invoice'] );
        }
        $invoice = Invoice::findOrNew( $id );
        $invoice->fill( $data );
        $invoice->save();

        $this->saveInvoiceDocument( Arr::get( $data, 'documents', [] ), $invoice->id );

        return $invoice;
    }

    public function invoiceSummary ( array $filters = [] )
    {
        $query = Invoice::query()
            ->select( [
                DB::raw( 'IFNULL(CAST(SUM(invoices.total_amount) AS DECIMAL(12,2)), 0.00) AS total' ),
                DB::raw( 'IFNULL(CAST(SUM(invoices.paid_amount) AS DECIMAL(12,2)), 0.00) AS paid' ),
                DB::raw( 'IFNULL(CAST(SUM(invoices.adjustment_discount) AS DECIMAL(12,2)), 0.00) AS discount' ),
                DB::raw( 'IFNULL(CAST(SUM(invoices.total_amount) - SUM(invoices.paid_amount) AS DECIMAL(12,2)), 0.00) AS unpaid' ),
            ] )
            ->join( 'customers', 'invoices.customer_user_id', '=', 'customers.user_id' )
            ->whereNull( 'customers.deleted_at' );

        if ( optional(auth()->user())->role == Roles::CUSTOMER ) {
            $query->where( 'customer_user_id', auth()->user()->id );
        } elseif ( ! empty( $filters[ 'customer_user_id' ] ) ) {
            $query->where( 'customer_user_id', $filters[ 'customer_user_id' ] );
        }

        $data = $query->first()->toArray();

        $query = Invoice::select( DB::raw( 'CAST(SUM(paid_amount) AS DECIMAL(12,2)) AS partially' ) )
            ->whereRaw( 'paid_amount > 0 and paid_amount is not null and total_amount > (paid_amount + adjustment_damaged + adjustment_storage + adjustment_discount + adjustment_other)' )
            ->join( 'customers', 'invoices.customer_user_id', '=', 'customers.user_id' )
            ->whereNull( 'customers.deleted_at' );

        if ( optional( auth()->user() )->role == Roles::CUSTOMER ) {
            $query->where( 'customer_user_id', auth()->user()->id );
        } elseif ( ! empty( $filters[ 'customer_user_id' ] ) ) {
            $query->where( 'customer_user_id', $filters[ 'customer_user_id' ] );
        }

        $data[ 'partially' ] = data_get( $query->first(), 'partially', "0.00" );

        return $data;
    }

    /**
     * @param null $customerUserId
     *
     * @return array
     */
    public function graphicalNotation ( $customerUserId = null ): array
    {
        $customer = app( CustomerService::class )->getCustomerByUserId( $customerUserId );
        $invoices = DB::table( 'invoices')->select( [
            'ar_number',
            DB::raw( 'total_amount - invoices.paid_amount AS due_amount' ),
        ] )
            ->join( 'exports', 'exports.id', '=', 'invoices.export_id' )
            ->join( 'customers', 'invoices.customer_user_id', '=', 'customers.user_id' )
            ->where( 'invoices.customer_user_id', $customerUserId )
            ->get();

        return [
            'customer_name' => optional( $customer )->customer_name,
            'invoices'      => $invoices,
        ];
    }

    /**
     * @param null $customerUserId
     *
     * @return array
     */
    public function monthlyGraph ( $customerUserId = null ): array
    {
        $customer = app( CustomerService::class )->getCustomerByUserId( $customerUserId );
        $invoices = DB::table( 'invoices')->select( [
            DB::raw("DATE_FORMAT(invoices.created_at, '%m-%Y') AS month"),
            DB::raw( 'ROUND(sum(total_amount), 2) AS total_amount' ),
            DB::raw( 'ROUND(sum(adjustment_discount), 2) as total_discount' ),
            DB::raw( 'ROUND(sum(total_amount) - SUM(paid_amount), 2) as total_pending' ),
        ] )
            ->join( 'exports', 'exports.id', '=', 'invoices.export_id' )
            ->join( 'customers', 'invoices.customer_user_id', '=', 'customers.user_id' )
            ->where( 'invoices.customer_user_id', $customerUserId )
            ->groupBy(DB::raw("DATE_FORMAT(invoices.created_at, '%Y%m')"))
            ->get();

        return [
            'customer_name' => optional( $customer )->customer_name,
            'invoices'      => $invoices,
        ];
    }

    private function removeInvoiceDocuments ( $invoice_id, $newDocs )
    {
        $ids = VehicleDocument::where( [
            'invoice_id' => $invoice_id,
        ] )->whereNotIn( 'id', collect( $newDocs )->reject( function ( $url ) {
            return filter_var($url, FILTER_VALIDATE_URL);
        })->pluck( 'id' )->toArray() )
            ->pluck( 'id' )
            ->toArray();

        VehicleDocument::whereIn( 'id', $ids )->delete();
    }

    private function saveInvoiceDocument ( $documents, $invoiceId )
    {
        foreach ( $documents as $url ) {
            if ( filter_var($url, FILTER_VALIDATE_URL) ) {
                $invoiceDoc = new VehicleDocument();
                $invoiceDoc->name = str_replace( env( 'AWS_S3_BASE_URL' ), '', $url );
                $invoiceDoc->invoice_id = $invoiceId;
                $invoiceDoc->save();
            }
        }
    }
}
