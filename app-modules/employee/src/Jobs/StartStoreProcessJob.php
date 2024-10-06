<?php

namespace Modules\Employee\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Modules\Employee\Services\import\BatchProcessingService;
use Spatie\SimpleExcel\SimpleExcelReader;

class StartStoreProcessJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(private readonly string $fullFilePath, private readonly string $userId) {}

    /**
     * Execute the job.
     *
     * @throws \Throwable
     */
    public function handle(BatchProcessingService $service): void
    {
        $fileData = SimpleExcelReader::create($this->fullFilePath)->getRows();

        $service->initiateStoreProcess($fileData, $this->fullFilePath, $this->userId);
    }
}
