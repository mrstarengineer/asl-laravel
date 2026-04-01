<?php

namespace App\Console\Commands;

use App\Models\Pricing;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class PricingDocumentMigration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:pricing-documents';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate Pricing Document';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct ()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle ()
    {
        try {
            foreach ( Pricing::orderBy( 'id', 'desc' )->cursor() as $pricing ) {
                $save_path = $this->migratePricingDocument( $pricing->upload_file );
                if ( $save_path ) {
                    $pricing->update( ['upload_file' => $save_path] );
                }
                $this->info( 'Documents migrated successfully for pricing id:  ' . $pricing->id );
            }
        } catch ( \Exception $e ) {
            $this->error( 'Something went wrong' );
        }

        return 0;
    }

    private function migratePricingDocument ( $url )
    {
        try {
            $path = 'http://44.242.105.86/uploads/' . basename( $url );
            $save_path = 'uploads/pricing/' . basename( $path );

            Storage::put( $save_path, file_get_contents( $this->escapefile_url( $path ) ) );
            dump( $save_path );

            return $save_path;
        } catch ( \Exception $e ) {
            $this->error( $e->getMessage() );
        }
    }

    function escapefile_url ( $url )
    {
        $parts = parse_url( $url );
        $path_parts = array_map( 'rawurldecode', explode( '/', $parts[ 'path' ] ) );

        return
            $parts[ 'scheme' ] . '://' .
            $parts[ 'host' ] .
            implode( '/', array_map( 'rawurlencode', $path_parts ) );
    }

}
