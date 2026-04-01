<?php


namespace App\Services\ActivityLog;


use App\Models\ActivityLog;
use App\Services\BaseService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class ActivityLogService extends BaseService
{
    public function all ( array $filters = [] )
    {
        $query = ActivityLog::query();

        if ( ! empty( $filters[ 'type' ] ) ) {
            $query->where( 'type', $filters[ 'type' ] );
        }

        if ( ! empty( $filters[ 'user_id' ] ) ) {
            $query->where( 'user_id', $filters[ 'user_id' ] );
        }

        if ( ! empty( $filters[ 'title' ] ) ) {
            $query->where( DB::raw( 'LOWER(title)' ), 'LIKE', '%' . strtolower( $filters[ 'title' ] ) . '%' );
        }

        $query->orderBy( 'id', 'desc' );
        $limit = Arr::get( $filters, 'limit', 20 );

        return $limit != '-1' ? $query->paginate( $limit ) : $query->paginate( 1000 );
    }

    public function getById ( $id )
    {
        return ActivityLog::find( $id );
    }

    public function store ( array $data )
    {
        return $this->saveActivity( $data );
    }

    private function saveActivity ( $data, $id = null )
    {
        $log = ActivityLog::findOrNew( $id );
        $log->fill( $data );
        $log->save();

        return $log;
    }
}
