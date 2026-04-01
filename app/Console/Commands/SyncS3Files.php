<?php

namespace App\Console\Commands;

use App\Models\Export;
use App\Models\ExportImage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class SyncS3Files extends Command
{
    // Command signature and description
    protected $signature = 'sync:s3files {container_number}';
    protected $description = 'Sync all files from a specific S3 folder to the database';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $container_number = $this->argument('container_number');

        $id = Export::where('container_number', $container_number)->first()->id;

        $folder = $id;
        // Ensure the folder ends with a '/' for proper prefixing
        if ( substr($folder, -1) !== '/' ) {
            $folder .= '/';
        }

        $finalPath = 'uploads/exports/images/' . $folder;

        // Fetch all files from the specified folder
        $files = Storage::disk('s3')->allFiles($finalPath);

        // Array with 'thumb'
        $thumbImages = preg_grep('/thumb/', $files);

        // Array without 'thumb'
        $nonThumbImages = preg_grep('/thumb/', $files, PREG_GREP_INVERT);

        if ( empty($files) ) {
            $this->info("No files found in folder: $folder");
            return;
        }

        // Iterate over each file and save or update the database
        $this->saveFilePath($thumbImages, $nonThumbImages, $id);

        $this->info('File synchronization complete!');
    }

    /**
     * Save or update the file path in the database.
     */
    protected function saveFilePath( $thumbImages, $nonThumbImages, $id )
    {
        $totalInsert = 0;
        foreach ( $nonThumbImages as $file ) {

            $pathParts = explode('/', $file);
            $lastIndex = count($pathParts) - 1;
            $filename = $pathParts[$lastIndex];
            $pathParts[$lastIndex] = 'thumb-' . $filename;
            $newFilePath = implode('/', $pathParts);

            $thumbnail = null;
            if ( in_array($newFilePath, $thumbImages) ) {
                $thumbnail = $newFilePath;
            }

            if ( $file && $thumbnail ) {

                $fileExits = ExportImage::where([
                    'name'      => $file,
                    'export_id' => $id,
                ])->first();


                if ( $fileExits ) {
                    $this->info("File already exists: $file");
                } else {
                    ExportImage::create([
                        'name'      => $file,
                        'thumbnail' => $thumbnail,
                        'export_id' => $id,
                        'type'      => 1,
                        'baseurl'   => 'recover_from_s3',
                    ]);

                    $totalInsert ++;
                    $this->info("File saved: $file");
                }
            }
        }

        $this->info("Total File: ". count($nonThumbImages) . " Total insert :". $totalInsert);

    }
}
