<?php


namespace App\Services\User;


use App\Models\User;
use App\Services\BaseService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class UserService extends BaseService {

	/**
	 * @param array $filters
	 *
	 * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
	 */
	public function all( array $filters = [] )
    {
        $query = User::query();

        if ( ! empty( $filters[ 'username' ] ) ) {
            $query->where( DB::raw( 'LOWER(username)' ), 'LIKE', '%' . strtolower( $filters[ 'username' ] ) . '%' );
        }

        if ( ! empty( $filters[ 'email' ] ) ) {
            $query->where( DB::raw( 'LOWER(email)' ), 'LIKE', '%' . strtolower( $filters[ 'email' ] ) . '%' );
        }

        if ( isset( $filters[ 'status' ] ) ) {
            $query->where( 'status', $filters[ 'status' ] );
        }

        if ( isset( $filters[ 'role' ] ) ) {
            $query->where( 'role', $filters[ 'role' ] );
        }

        $limit = Arr::get( $filters, 'limit', 20 );

		return $limit != '-1' ? $query->paginate( $limit ) : $query->get();
	}

	/**
	 * @param $id
	 *
	 * @return mixed
	 */
	public function getById( $id ) {
		return User::find( $id );
	}

	/**
	 * @param array $data
	 *
	 * @return mixed
	 */
	public function store( array $data ) {
		return $this->saveUser( $data );
	}

	/**
	 * @param $id
	 * @param array $data
	 *
	 * @return mixed
	 */
	public function update( $id, array $data ) {
		return $this->saveUser( $data, $id );
	}

	/**
	 * @param $id
	 *
	 * @return mixed
	 */
	public function destroy( $id ) {
		return User::find( $id )->delete();
	}

	/**
	 * @param $data
	 * @param null $id
	 *
	 * @return mixed
	 */
	private function saveUser( $data, $id = null ) {
        if ( empty( $data[ 'password' ] ) ) {
            unset( $data[ 'password' ] );
        }

        $user = User::findOrNew( $id );
        $user->fill( $data );
        if ( ! empty( $data[ 'password' ] ) ) {
            $user->password = bcrypt( $data[ 'password' ] );
        }
        $user->save();

        return $user;
    }
}
