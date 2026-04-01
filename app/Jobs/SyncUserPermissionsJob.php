<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncUserPermissionsJob implements ShouldQueue
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
    private $permissions;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( $roleId, $permissions )
    {
        $this->roleId = $roleId;
        $this->permissions = $permissions;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach ( User::where( 'role', $this->roleId )->cursor() as $user ) {
            $user->syncPermissions( $this->permissions );
            $user->update( [ 'authentication_required' => 1 ] );
        }
    }
}
