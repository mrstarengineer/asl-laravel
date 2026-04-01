<?php

namespace App\Jobs;

use App\Models\Role;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncSingleUserPermissionsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 180;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 1;

    private $roleId;
    private $userId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( $roleId, $userId )
    {
        $this->roleId = $roleId;
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $rolePermissions = Role::find( $this->roleId )->permissions()->pluck( 'permission_id' )->toArray();
        User::find( $this->userId )->syncPermissions( $rolePermissions );
    }
}
