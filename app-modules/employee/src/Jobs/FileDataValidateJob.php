<?php

namespace Modules\Employee\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Modules\Employee\Services\import\ValidationService;

class FileDataValidateJob implements ShouldQueue
{
    use Queueable, Batchable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private readonly array $rowData,
        private readonly int $index,
        private readonly string $cacheKey
    ) {}

    /**
     * Execute the job.
     *
     * @throws \Illuminate\Contracts\Cache\LockTimeoutException
     */
    public function handle(ValidationService $service): void
    {
        $service->validateRowData($this->rowData, $this->index, $this->cacheKey);
    }
}
