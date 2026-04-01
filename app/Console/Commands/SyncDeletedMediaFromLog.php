<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;


class SyncDeletedMediaFromLog extends Command
{

    protected $signature = 'sync:container-media-with-log {containerNumber}';

    protected $description = 'Check activity log and mark images as deleted';

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
        $containerNumber = trim($this->argument('containerNumber'));

        $export = DB::table('exports')->where('container_number', $containerNumber)->first();


        if (!$export) {
            $this->warn('Invalide Container Number. Please provide valid Container Number');
            return;
        }


        $activityLog = DB::table('activity_logs')
            ->where('title', 'like', "%Container Number: $containerNumber")
            ->orderBy('created_at', 'desc')
            ->get();


        if (count($activityLog) == 0) {
            $this->warn('No data in Activity log for this Container Number. Please provide valid Container Number');
            return;
        }


        $lastUpdatedImages = [];
        foreach ($activityLog as $log) {
            $requestData = json_decode($log->request_data, true);

            // Check for JSON decode errors
            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->error('JSON Error decoding request_data: ' . json_last_error_msg());
                return;
            }

            $requestLogImages = $requestData['fileurl']['container_images'] ?? [];

            if(count($requestLogImages) > 0) {
                $lastUpdatedImages = $requestLogImages;
                break;
            }
        }

        DB::table('export_images')->where('export_id', $export->id)->update(['deleted_at' => null]);
        $existingDBImages = DB::table('export_images')->where('export_id', $export->id)->get();

        // Check for JSON decode errors
        if (empty($existingDBImages)) {
            $this->warn('There is no container images for this Container Number ' . $containerNumber);
            return;
        }

        $imageToBeDeleted = [];

        // Mark images as deleted if they exist in the database but not in the request data
        foreach ($existingDBImages as $existingDbImage) {
            $existsInDbAndLog = false;

            foreach ($lastUpdatedImages as $imageInLog) {
                $logImageName = is_array($imageInLog) ? $imageInLog['url'] :  $imageInLog;

                if (str_replace(env('AWS_S3_BASE_URL', 'https://asl-shipping.s3.me-south-1.amazonaws.com/'), '', $logImageName) === $existingDbImage->name) {
                    $existsInDbAndLog = true;
                    break;
                }
            }

            // If the imageInLog does not exist in the request data, mark it for latter deletion
            if (!$existsInDbAndLog) {
                $imageToBeDeleted[] = $existingDbImage->name;
            }
        }

        DB::table('export_images')
            ->where('export_id', $export->id)
            ->whereIn('name', $imageToBeDeleted)
            ->update(['deleted_at' => now()]);


        $this->info(count($imageToBeDeleted).' images are marked as deleted successfully : ');

        return Command::SUCCESS;
    }
}
