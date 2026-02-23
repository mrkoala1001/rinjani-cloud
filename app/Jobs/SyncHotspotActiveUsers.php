<?php

namespace App\Jobs;

use App\Services\MikrotikSyncService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SyncHotspotActiveUsers implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $owners = \App\Models\User::where('role', 'owner')->where('is_active', true)->get();
        foreach ($owners as $owner) {
             try {
                 (new MikrotikSyncService($owner))->syncHotspot();
             } catch (\Exception $e) {
                 \Log::error("Job Failed for Owner {$owner->id}: " . $e->getMessage());
             }
        }
    }
}
