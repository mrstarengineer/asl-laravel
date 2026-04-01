<?php


namespace App\Services\Complain;


use App\Enums\ReadStatus;
use App\Enums\Roles;
use App\Models\Complain;
use App\Services\BaseService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class ComplainService extends BaseService
{
    public function all ( array $filters = [] )
    {
        $query = Complain::with([ 'customer' ]);

        if ( isset( $filters[ 'status' ] ) ) {
            $query->where( 'status', $filters[ 'status' ] );
        }

        if ( optional( auth()->user() )->role == Roles::CUSTOMER ) {
            $query->where( 'customer_user_id', auth()->user()->id );
        }

        if ( isset( $filters[ 'subject' ] ) ) {
            $query->where( DB::raw( 'LOWER(subject)' ), 'LIKE', '%' . strtolower( $filters['subject'] ) . '%' );
        }

        $limit = Arr::get( $filters, 'limit', 20 );

        $orderByCol = Arr::get( $filters, 'order_by_column', 'id' );
        $query->orderBy( empty( $orderByCol ) ? 'id' : $orderByCol, Arr::get( $filters, 'order_by', 'desc' ) );

        return $limit != '-1' ? $query->paginate( $limit ) : $query->get();
    }

    public function getById ( $id )
    {
        return Complain::with([ 'customer', 'conversations.sender' ])->find( $id );
    }

    public function store ( array $data )
    {
        return $this->saveComplain( $data );
    }

    public function update ( $id, array $data )
    {
        return $this->saveComplain( $data, $id );
    }

    public function destroy ( $id )
    {
        return Complain::find( $id )->delete();
    }

    private function saveComplain ( $data, $id = null )
    {
        $complain = Complain::findOrNew( $id );
        $complain->fill( $data );
        $complain->save();

        return $complain;
    }

    public function adminUnreadCount ()
    {
        return Complain::where( 'read_by_admin', ReadStatus::UNREAD )->count();
    }
}
