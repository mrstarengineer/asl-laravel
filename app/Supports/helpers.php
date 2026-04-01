<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Log\Logger;
use Illuminate\Support\Facades\Auth;
use Ramsey\Uuid\Uuid;

if ( !function_exists( 'auth' ) ) {
    /**
     * Get the available auth instance.
     *
     * @param string|null $guard
     * @return \Illuminate\Support\Facades\Auth
     */
    function auth( $guard = null )
    {
        if ( is_null( $guard ) ) {
            $guards = array_keys( config( 'auth.guards' ) );
            $auth = app( 'auth' );
            foreach ( $guards as $grd ) {
                if ( $auth->guard( $grd )->check() ) {
                    return $auth->guard( $grd );
                }
            }

            return $auth;
        }

        return app( 'auth' )->guard( $guard );
    }
}

if ( !function_exists( 'json_response' ) ) {
    function json_response( $response_data = null, $status_code = 200, $errors = [] )
    {
        $response = $response_data;

        if ( count( $errors ) > 0 || $status_code >= 300 ) {
            $response = array_filter( [ 'status' => 'error', 'errors' => $errors, 'data' => $response ] );
        }

        if ( blank( $response ) ) {
            $response = [];
        }

        return response()->json( $response, $status_code );
    }
}

if ( !function_exists( 'trans_table_column' ) ) {
    function trans_table_column( $column )
    {
        if ( !is_array( $column ) && !is_object( $column ) ) return $column;

        $locale = strtolower( app()->getLocale() ?? 'en' );
        return data_get( $column, $locale, null ) ?? data_get( $column, 'en' );
    }
}

if ( !function_exists( 'api' ) ) {
    /**
     * @param $data
     * @return \App\Supports\ApiJsonResponse
     */
    function api( $data = [] )
    {
        $api = new \App\Supports\ApiJsonResponse( $data );

        return $api;
    }
}

if ( !function_exists( 'to_array' ) ) {
    function to_array( $data )
    {
        if ( $data instanceof Collection ) {
            return $data->toArray();
        }

        if ( $data instanceof Model || $data instanceof \Illuminate\Pagination\LengthAwarePaginator ) {
            return $data->toArray();
        }

        if ( is_object( $data ) ) {
            return (array) $data;
        }

        return $data;
    }
}

if ( !function_exists( 'decimal_point' ) ) {
    function decimal_point( $number, $point = 2 )
    {
        return number_format( (float) $number, $point, '.', '' );
    }
}

if ( !function_exists( 'db_escape' ) ) {
    function db_escape( $value, $connection = null )
    {
        return \DB::connection( $connection )->getPdo()->quote( $value );
    }
}

if ( !function_exists( 'request' ) ) {
    /**
     * @param null $key
     * @return \Illuminate\Http\Request|mixed
     */
    function request( $key = null )
    {
        if ( $key ) {
            return app( 'request' )->input( $key );
        }

        return app( 'request' );
    }
}

if ( !function_exists( 'debuglog' ) ) {
    function debuglog( $msg, $context = [], $level = 'debug' )
    {
        if ( !config( 'logging.enable' ) ) {
            return;
        }

        /**
         * @var $logger Logger
         */
        $logger = app( Logger::class );

        if ( in_array( gettype( $context ), [
            'int',
            'integer',
            'float',
            'double',
            'string',
        ] ) ) {
            $context = [ $context ];
        }

        if ( !is_array( $context ) ) {
            $context = [];
        }

        $logger->channel( 'debug' )->write( $level, $msg, $context );
    }

}


if ( !function_exists( 'uuid' ) ) {
    function uuid( $name )
    {
        return Uuid::uuid5( Uuid::uuid4(), $name );
    }
}

if ( !function_exists( 'config_path' ) ) {
    function config_path( $path = '' )
    {
        return app()->basePath() . '/config' . ( $path ? '/' . $path : $path );
    }
}

if ( !function_exists( 'debug_log' ) ) {
    function debug_log( $msg, $context = [], $level = 'info' )
    {
        /**
         * @var $logger Logger
         */
        $logger = app( Logger::class );

        if ( config( 'logging.enable' ) ) {
            if ( !is_array( $context ) ) {
                $context = [];
            }

            $logger->write( $level, $msg, $context );
        }
    }
}

if ( !function_exists( 'startsWith' ) ) {
    function startsWith( $haystack, $needle )
    {
        $length = strlen( $needle );
        return substr( $haystack, 0, $length ) === $needle;
    }
}

if ( !function_exists( 'endsWith' ) ) {
    function endsWith( $haystack, $needle )
    {
        $length = strlen( $needle );
        if ( !$length ) {
            return true;
        }
        return substr( $haystack, -$length ) === $needle;
    }
}

if ( !function_exists( 'store_activity' ) ) {
    function store_activity( $title, $logs = [], $modelId = null, $type = \App\Enums\ActivityType::CREATE )
    {
        $data = [
            'user_id'      => optional( auth()->user() )->id,
            'title'        => $title,
            'type'         => $type,
            'logs'         => $logs,
            'request_data' => request()->all(),
            'model_id'     => $modelId,
        ];

        if ( request()->header( 'asl-platform' ) ) {
            $data[ 'platform' ] = strtoupper( request()->header( 'asl-platform' ) );
        }

        return \App\Models\ActivityLog::create( $data );
    }
}

if ( !function_exists( 'uri_file_exists' ) ) {
    function uri_file_exists( $url )
    {
        $headers = get_headers( $url );

        return (bool) stripos( $headers[ 0 ], "200 OK" );
    }
}

if ( !function_exists( 'force_login' ) ) {
    function force_login( $userId )
    {
        $user = App\Models\User::find( $userId );
        Auth::login( $user );

        return Auth::user();
    }
}
