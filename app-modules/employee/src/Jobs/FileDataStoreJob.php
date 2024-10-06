<?php

namespace Modules\Employee\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Modules\Employee\DTO\EmployeeData;
use Modules\User\Models\User;
use Modules\Employee\Services\EmployeeService;

class FileDataStoreJob implements ShouldQueue
{
    use Queueable, Batchable;

    /**
     * Create a new job instance.
     */
    public function __construct(private readonly array $data, private string $userId) {}

    /**
     * Execute the job.
     */
    public function handle(EmployeeService $service): void
    {
        $service->create(User::find($this->userId), EmployeeData::from($this->data));
    }
}
