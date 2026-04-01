<?php


namespace App\Services\MediaMigrate;


use App\Enums\ExportPhotoType;
use App\Models\ClaimImage;
use App\Models\CustomerDocument;
use App\Models\Export;
use App\Models\ExportImage;
use App\Models\Invoice;
use App\Models\Vehicle;
use App\Models\VehicleDocument;
use App\Models\VehicleImage;
use App\Services\BaseService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class MediaMigrateService extends BaseService
{
    /**
     * Migrate Customer Document files
     *
     * @param $customerUserId
     */
    public function migrateCustomerDocuments ( $customerUserId )
    {
        $documents = CustomerDocument::where( 'customer_user_id', $customerUserId )->get();
        foreach ( $documents as $document ) {
            try {
                $path = 'http://44.242.105.86/uploads/' . basename($document->file);
                $save_path = 'uploads/customers/documents/' . $customerUserId . '/' . basename( $path );

                Storage::put( $save_path, $this->file_get_contents_curl( $this->escapefile_url( $path ) ) );
                $document->update( [ 'file' => $save_path ] );
            } catch ( \Exception $e ) {
                Log::error( $e->getMessage() );
            }
        }
    }

    /**
     * Migrate Single vehicle document
     *
     * @param $document
     */
    public function migrateCustomerDocument ( $document )
    {
        if ( ! $document instanceof CustomerDocument ) {
            $document = CustomerDocument::find( $document );
        }

        if ( $document && $document->file ) {
            try {
                $path = 'http://44.242.105.86/uploads/' . basename( $document->file );
                $save_path = 'uploads/customers/documents/' . $document->customer_user_id;
                if ( ! file_exists( $save_path ) ) {
                    mkdir( $save_path, 0777, true );
                }

                Storage::put( $save_path . '/' . basename( $path ), $this->file_get_contents_curl( $this->escapefile_url( $path ) ) );
                $document->update( [ 'file' => 'uploads/customers/documents/' . $document->customer_user_id . '/' . basename( $path ) ] );
            } catch ( \Exception $e ) {
                dump( $e->getMessage() );
                Log::error( $e->getMessage() );
            }
        }
    }

    /**
     * Migrate Vehicle Images and Documents
     *
     * @param $vehicleId
     */
    public function migrateVehicleMediaFiles ( $vehicleId )
    {
        try {
            $vehicle = Vehicle::find( $vehicleId );
            if ( $vehicle->photos_migrated == 0 ) {
                $this->migrateVehicleImages( $vehicle->id );
                $vehicle->update( [ 'photos_migrated' => 1 ] );
            }

            if ( $vehicle->documents_migrated == 0 ) {
                $this->migrateVehicleDocuments( $vehicle->id );
                $vehicle->update( [ 'documents_migrated' => 1 ] );
            }
        } catch ( \Exception $e ) {
            Log::error( $e->getMessage() );
        }
    }

    private function migrateVehicleImages ( $vehicleId )
    {
        $images = VehicleImage::where( 'vehicle_id', $vehicleId )->orderBy( 'id', 'desc' )->get();
        foreach ( $images as $image ) {
            $this->migrateVehicleImage( $image );
        }
    }

    public function migrateVehicleImage ( $image )
    {
        if ( ! $image instanceof VehicleImage ) {
            $image = VehicleImage::find( $image );
        }

        if ( $image && $image->vehicle_id ) {
            try {
                $path = 'http://44.242.105.86/uploads/' . basename( $image->name );
                $thumbnailPath = 'http://44.242.105.86/uploads/' . basename( $image->thumbnail );
                $save_path = 'uploads/vehicles/images/' . $image->vehicle_id;
                if ( ! file_exists( $save_path ) ) {
                    mkdir( $save_path, 0777, true );
                }

                if ( ! Storage::exists( $save_path . '/' . basename( $path ) )) {
                    Storage::put( $save_path . '/' . basename( $path ), $this->file_get_contents_curl( $this->escapefile_url( $path ) ) );
                }
                if ( ! Storage::exists( $save_path . '/' . basename( $thumbnailPath ) ) ) {
                    Storage::put( $save_path . '/' . basename( $thumbnailPath ), $this->file_get_contents_curl( $this->escapefile_url( $thumbnailPath ) ) );
                }
                $image->update( [ 'name' => $save_path . '/' . basename( $path ), 'thumbnail' => $save_path . '/' . basename( $thumbnailPath ) ] );
            } catch ( \Exception $e ) {
                dump($e->getMessage());
                Log::error( $e->getMessage() );
            }
        }
    }

    private function migrateVehicleDocuments ( $vehicleId )
    {
        $documents = VehicleDocument::where( 'vehicle_id', $vehicleId )->orderBy( 'id', 'desc' )->get();
        foreach ( $documents as $document ) {
            $this->migrateVehicleDocument( $document );
        }
    }

    /**
     * Migrate Single vehicle document
     *
     * @param $document
     */
    public function migrateVehicleDocument ( $document )
    {
        if ( ! $document instanceof VehicleDocument ) {
            $document = VehicleDocument::find( $document );
        }

        if ( $document && $document->name ) {
            try {
                $path = 'http://44.242.105.86/uploads/' . basename( $document->name );
                $save_path = 'uploads/vehicles/documents/' . $document->vehicle_id;
                if ( ! file_exists( $save_path ) ) {
                    mkdir( $save_path, 0777, true );
                }

                Storage::put( $save_path . '/' . basename( $path ), $this->file_get_contents_curl( $this->escapefile_url( $path ) ) );
                $document->update( [ 'name' => 'uploads/vehicles/documents/' . $document->vehicle_id . '/' . basename( $path ) ] );
            } catch ( \Exception $e ) {
                dump( $e->getMessage() );
                Log::error( $e->getMessage() );
            }
        }
    }

    /**
     * Migrate Export Images and Invoice
     *
     * @param $exportId
     */
    public function migrateExportMediaFiles ( $exportId )
    {
        try {
            $export = Export::find( $exportId );
            if ( $export->photos_migrated == 0 ) {
                $this->migrateExportImages( $export->id );
                $export->update( [ 'photos_migrated' => 1 ] );
                dump( 'images migrated successfully for export id:  ' . $export->id );
            }

            if ( $export->documents_migrated == 0 && ! empty( $export->export_invoice ) ) {
                $this->migrateContainerDocument($export);
            }
        } catch ( \Exception $e ) {
            dump($e->getMessage());
            Log::error( $e->getMessage() );
        }
    }

    private function migrateExportImages ( $exportId )
    {
        $images = ExportImage::where( 'export_id', $exportId )->orderBy( 'id', 'desc' )->get();
        foreach ( $images as $image ) {
            $this->migrateContainerPhoto( $image );
        }
    }

    public function migrateContainerPhoto ( $image )
    {
        if ( ! $image instanceof ExportImage ) {
            $image = ExportImage::find( $image );
        }

        if ( $image && $image->export_id ) {
            try {
                $path = 'http://44.242.105.86/uploads/' . basename( $image->name );
                $thumbnailPath = 'http://44.242.105.86/uploads/' . basename( $image->thumbnail );
                $save_path = 'uploads/exports/images/' . $image->export_id . '/';

                if ( ! Storage::exists( $save_path . basename( $path ) ) ) {
                    Storage::put( $save_path . basename( $path ), $this->file_get_contents_curl( $this->escapefile_url( $path ) ) );
                }
                if ( ! Storage::exists( $save_path . basename( $thumbnailPath ) ) ) {
                    Storage::put( $save_path . basename( $thumbnailPath ), $this->file_get_contents_curl( $this->escapefile_url( $thumbnailPath ) ) );
                }
                $image->update( [ 'name' => $save_path . basename( $path ), 'thumbnail' => $save_path . basename( $thumbnailPath ) ] );
            } catch ( \Exception $e ) {
                dump($e->getMessage());
                Log::error( $e->getMessage() );
            }
        }
    }

    public function migrateContainerDocument ( $export )
    {
        if ( ! $export instanceof Export ) {
            $export = Export::find( $export );
        }

        if ( $export && $export->export_invoice ) {
            try {
                $path = 'http://44.242.105.86/uploads/' . basename( $export->export_invoice );
                $save_path = 'uploads/exports/documents/' . $export->id;
                $save_path .= '/' . str_replace( ' ', '', basename( $path ) );

                Storage::put( $save_path, $this->file_get_contents_curl( $this->escapefile_url( $path ) ) );
                $export->update( [ 'documents_migrated' => 1, 'export_invoice' => 'uploads/exports/documents/' . $export->id . '/' . str_replace( ' ', '', basename( $path ) ) ] );
            } catch ( \Exception $e ) {
                dump($e->getMessage());
                Log::error($e->getMessage());
            }
        }
    }

    function escapefile_url ( $url )
    {
        $parts = parse_url( $url );
        $path_parts = array_map( 'urldecode', explode( '/', $parts[ 'path' ] ) );

        return
            $parts[ 'scheme' ] . '://' .
            $parts[ 'host' ] .
            implode( '/', array_map( 'urlencode', $path_parts ) );
    }

    public function migrateInvoiceDocuments ( $invoiceId )
    {
        try {
            $invoice = Invoice::find($invoiceId);
            $updatedData = [ 'documents_migrated' => 1 ];
            if ( ! empty($invoice->upload_invoice) ) {
                $path = 'http://44.242.105.86/uploads/' . basename( $invoice->upload_invoice );
                $save_path = 'uploads/invoices/' . basename( $path );
                Storage::put( $save_path, $this->file_get_contents_curl( $this->escapefile_url( $path ) ) );
                $updatedData['upload_invoice'] = $save_path;
            }
            if ( ! empty($invoice->clearance_invoice) ) {
                $path = 'http://44.242.105.86/uploads/' . basename( $invoice->clearance_invoice );
                $save_path = 'uploads/invoices/' . basename( $path );
                Storage::put( $save_path, $this->file_get_contents_curl( $this->escapefile_url( $path ) ) );
                $updatedData['clearance_invoice'] = $save_path;
            }
            $invoice->update( $updatedData );
        } catch ( \Exception $e ) {
            dump($e->getMessage());
            Log::error( $e->getMessage() );
        }
    }

    public function migrateVehicleThumbnailImages ( $imgId )
    {
        $image = VehicleImage::findOrFail( $imgId );
        try {
            $thumbnailPath = 'http://44.242.105.86/uploads/' . basename( $image->thumbnail );
            $save_path = 'uploads/vehicles/images/' . $image->vehicle_id;
            if ( ! file_exists( $save_path ) ) {
                mkdir( $save_path, 0777, true );
            }

            Storage::put( $save_path . '/' . basename( $thumbnailPath ), $this->file_get_contents_curl( $this->escapefile_url( $thumbnailPath ) ) );
            $image->update( [ 'thumbnail' => $save_path . '/' . basename( $thumbnailPath ) ] );
        } catch ( \Exception $e ) {
            dump($e->getMessage());
            Log::error( $e->getMessage() );
        }
    }

    public function migrateContainerThumbnailImages ( $export_id )
    {
        $images = ExportImage::where( 'export_id', $export_id )->orderBy( 'id', 'desc' )->get();
        foreach ( $images as $image ) {
            try {
                $thumbnailPath = 'http://44.242.105.86/uploads/' . basename( $image->thumbnail );
                $save_path = 'uploads/exports/images/' . $image->export_id . '/';
                if ( ! file_exists( $save_path ) ) {
                    mkdir( $save_path, 0777, true );
                }

                Storage::put( $save_path . basename( $thumbnailPath ), $this->file_get_contents_curl( $this->escapefile_url( $thumbnailPath ) ) );
                $image->update( [ 'thumbnail' => $save_path . basename( $thumbnailPath ) ] );
            } catch ( \Exception $e ) {
                dump($e->getMessage());
                Log::error( $e->getMessage() );
            }
        }
    }

    public function migrateClaimImage ( $image )
    {
        if ( ! $image instanceof ClaimImage ) {
            $image = ClaimImage::find( $image );
        }

        if ( $image && $image->claim_id ) {
            try {
                $path = 'http://44.242.105.86/uploads/' . basename( $image->image );
                $thumbnailPath = 'http://44.242.105.86/uploads/' . basename( $image->thumbnail );
                $save_path = 'uploads/vehicles/claim-images';
                if ( ! file_exists( $save_path ) ) {
                    mkdir( $save_path, 0777, true );
                }

                if ( ! Storage::exists( $save_path . '/' . basename( $path ) )) {
                    Storage::put( $save_path . '/' . basename( $path ), $this->file_get_contents_curl( $this->escapefile_url( $path ) ) );
                }
                if ( ! Storage::exists( $save_path . '/' . basename( $thumbnailPath ) ) ) {
                    Storage::put( $save_path . '/' . basename( $thumbnailPath ), $this->file_get_contents_curl( $this->escapefile_url( $thumbnailPath ) ) );
                }
                $image->update( [ 'image' => $save_path . '/' . basename( $path ), 'thumbnail' => $save_path . '/' . basename( $thumbnailPath ) ] );
            } catch ( \Exception $e ) {
                dump($e->getMessage());
                Log::error( $e->getMessage() );
            }
        }
    }

    function file_get_contents_curl( $url ) {

        $ch = curl_init();

        curl_setopt( $ch, CURLOPT_AUTOREFERER, TRUE );
        curl_setopt( $ch, CURLOPT_HEADER, 0 );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt( $ch, CURLOPT_URL, $url );
        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, TRUE );

        $data = curl_exec( $ch );
        curl_close( $ch );

        return $data;

    }
}
