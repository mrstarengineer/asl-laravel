<?php


namespace App\Services\Location;


use App\Enums\Roles;
use App\Models\Location;
use App\Services\BaseService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LocationService extends BaseService
{
    public function all(array $filters = [])
    {
        $query = Location::query();

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if ( !empty($filters['include_ids']) ) {
            $query->whereIn('id', $filters['include_ids']);
        }

        if ( !empty($filters['exclude_ids']) ) {
            $query->whereNotIn('id', $filters['exclude_ids']);
        }

        if ( in_array( optional( auth()->user() )->role, [ Roles::LOCATION_ADMIN, Roles::LOCATION_VIEW_ADMIN, Roles::LOCATION_RESTRICTED_ADMIN ] ) ) {
            $query->whereIn( 'id', auth()->user()->locations );
        }

        if (!empty($filters['q'])) {
            $query->where(DB::raw('LOWER(name)'), 'LIKE', '%'. strtolower($filters['q']) .'%');
        }

        // For China Location Allow Master Admin
        if(auth()->user()->role == 2) {
            if( ! in_array(16, optional(auth()->user())->locations ?? [] ) ) {
                $query->where('id', '!=', 16);
            }
        }else if(auth()->user()->role == 3){
            if( in_array(auth()->user()->id,  explode(",", env('CHINA_CUSTOMER_USER_IDS'))) ) {
                $query->where('id', '=', 16);
            }else {
                $query->where('id', '!=', 16);
            }
        } else if(  ! in_array(auth()->user()->role,  [0]  ) ) {
            $query->where('id', '!=', 16);
        }

        $limit = Arr::get($filters, 'limit', 20);

        return $limit != '-1' ? $query->paginate($limit) : $query->get();
    }

    public function getById($id)
    {
        return Location::find($id);
    }

    public function store(array $data)
    {
        return $this->saveLocation($data);
    }

    public function update($id, array $data)
    {
        return $this->saveLocation($data, $id);
    }

    public function destroy($id)
    {
        return Location::find($id)->delete();
    }

    private function saveLocation($data, $id = null)
    {
        $location = Location::findOrNew($id);
        $location->fill($data);
        $location->save();

        return $location;
    }
}
