<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Services\Storage\FileManager;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    /**
     * Update the user's profile information.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $user = $request->user();
        $requestData = $request->only('email');
        if ( $request->hasFile('photo') ) {
            $photoUrl = app( FileManager::class )->upload( $request->file( 'photo' ), 'uploads/users' );
            $requestData[ 'photo_url' ] = str_replace( env( 'AWS_S3_BASE_URL' ), '', $photoUrl );
        }

        return tap($user)->update($requestData);
    }
}
