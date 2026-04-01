<?php


namespace App\Services\Feedback;


use App\Enums\ReadStatus;
use App\Enums\Roles;
use App\Models\Feedback;
use App\Services\BaseService;
use Illuminate\Support\Arr;

class FeedbackService extends BaseService
{
    public function all ( array $filters = [] )
    {
        $query = Feedback::with('customer');

        if ( isset( $filters[ 'status' ] ) ) {
            $query->where( 'status', $filters[ 'status' ] );
        }

        if ( optional( auth()->user() )->role == Roles::CUSTOMER ) {
            $query->where( 'customer_user_id', auth()->user()->id );
        }

        $limit = Arr::get( $filters, 'limit', 20 );

        $orderByCol = Arr::get( $filters, 'order_by_column', 'id' );
        $query->orderBy( empty( $orderByCol ) ? 'id' : $orderByCol, Arr::get( $filters, 'order_by', 'desc' ) );

        return $limit != '-1' ? $query->paginate( $limit ) : $query->get();
    }

    public function getById ( $id )
    {
        return Feedback::with('customer')->find( $id );
    }

    public function store ( array $data )
    {
        return $this->saveFeedback( $data );
    }

    public function update ( $id, array $data )
    {
        return $this->saveFeedback( $data, $id );
    }

    public function destroy ( $id )
    {
        return Feedback::find( $id )->delete();
    }

    private function saveFeedback ( $data, $id = null )
    {
        $feedback = Feedback::findOrNew( $id );
        $feedback->fill( $data );
        $feedback->save();

        return $feedback;
    }

    public function adminUnreadCount ()
    {
        return feedback::where( 'read_by_admin', ReadStatus::UNREAD )->count();
    }
}
