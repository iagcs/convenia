<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Modules\User\Models\User;

class StartImportProcessJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(private User $user, private string $file) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {

    }
}
