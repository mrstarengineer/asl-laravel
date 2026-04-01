<?php


namespace App\Services\Storage;


use App\Services\BaseService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class FileManager extends BaseService
{
    public function upload( UploadedFile $file, $path = 'uploads', $fileName = null )
    {
        try {
            $ext = $file->getClientOriginalExtension();

            if( in_array( strtolower( $ext ), [ 'png', 'jpg', 'jpeg', 'svg', 'gif', 'heic' ] ) ) {
                $uploadedUrl = $this->uploadImageWithThumbnail( $file, $path );
            } else {
                if ( $fileName == null ) {
                    $fileName = Str::random( 10 ) . time() . '.' . $ext;
                }

                $uploadedUrl = Storage::url( $file->storeAS( $path, $fileName ) );
            }

            return $uploadedUrl;
        } catch ( \Exception $e ) {
            return false;
        }
    }

    /**
     * @param $filePath
     *
     * @return bool
     */
    public function delete( $filePath )
    {
        $success = true;
        try {
            if ( !@unlink( Storage::url( $filePath ) ) ) {
                $success = false;
            }
        } catch ( \Exception $e ) {
            $success = false;
        }

        return $success;
    }

    public function uploadImageWithThumbnail( $file, $path = 'uploads', $fileName = null )
    {
        if ( $fileName == null ) {
            $fileName = Str::random( 10 ) . time() . '.jpg';
        }

        $image = \Intervention\Image\ImageManagerStatic::make( $file );
        $image->orientate();
        $image->backup();

        if ( ! endsWith($path, '/') ) {
            $path .= '/';
        }

        // resize for main image
        $image->resize( null, 960, function ( $constraint ) {
            $constraint->aspectRatio();
        } )->encode( 'jpg', 100 );
        Storage::put( $path . $fileName, $image->stream() );

        // save thumbnail
        $image->reset();
        $image->resize( null, 240, function ( $constraint ) {
            $constraint->aspectRatio();
        } )->encode( 'jpg' );
        Storage::put( $path . 'thumb-' . $fileName, $image->stream() );

        return Storage::url( $path . $fileName );
    }

}
