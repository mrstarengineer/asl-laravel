<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UploadExportDocs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:upload_export_invoice';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        try {

            $output = [];

            $files = File::files(storage_path('app/public/exports'));

            foreach ($files as $key => $file) {

                Log::info("Starting ". $key);

                // ✅ Skip non-pdf
                if ($file->getExtension() !== 'pdf') {
                    Log::info('not pdf extension');
                    continue;
                }

                $filename = pathinfo($file->getFilename(), PATHINFO_FILENAME);
                $parts = preg_split('/\s+/', trim($filename));

                $ar_no = $parts[0] ?? null;
                $container_no = $parts[1] ?? null;

                if (!$ar_no || !$container_no) {
                    Log::warning("Invalid filename format: {$filename}");
                    continue;
                }

                // ✅ Find export
                $export = DB::table('exports')
                    ->where('ar_number', $ar_no)
                    ->where('container_number', $container_no)
                    ->first();

                if (!$export) {
                    Log::warning("No export found for: {$filename}");
                    continue;
                }

                // ✅ Skip if already stored in DB (FAST)
//                if (!empty($export->export_invoice)) {
//                    Log::info('already exists :'.  Storage::disk('s3')->url($export->export_invoice));
//                    continue;
//                }

                $finalName = "{$ar_no}_{$container_no}.pdf";
                $s3Path = "uploads/exports/documents/{$export->id}/{$finalName}";

                // ✅ Optional: S3 check (slower, but safe)
                if (Storage::disk('s3')->exists($s3Path)) {

                    $output[]['id'] = $export->id;
                    $output[]['export_invoice'] = $s3Path;

                    Log::info("Already exists in S3 ulr: {$s3Path} and export id : $export->id");

                    DB::table('exports')
                        ->where('id', $export->id)
                        ->update([
                            'export_invoice' => $s3Path
                        ]);

                    continue;
                }

                $uploadPath = "uploads/exports/documents/{$export->id}";

                // ✅ Upload (best method)
                Storage::disk('s3')->putFileAs(
                    $uploadPath,
                    $file,
                    $finalName
                );

                DB::table('exports')
                    ->where('id', $export->id)
                    ->update([
                        'export_invoice' => $uploadPath
                    ]);

                $output[]['id'] = $export->id;
                $output[]['export_invoice'] = $uploadPath;

                Log::info("Uploaded & updated: {$s3Path}");

            }

            Log::info("output", $output);

            Log::info("done ");

        } catch (\Exception $e) {
            Log::error('Error: ' . $e->getMessage());
        }
    }
}
