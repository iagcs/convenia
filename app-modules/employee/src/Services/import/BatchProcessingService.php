<?php

namespace Modules\Employee\Services\import;

use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\LazyCollection;
use Modules\Employee\Jobs\FileDataStoreJob;
use Modules\Employee\Jobs\FileDataValidateJob;

readonly class BatchProcessingService
{
    public function __construct(private ImportProcessOutcomeService $importNotificationService) {}

    /**
     * @throws \Throwable
     */
    public function initiateValidationProcess(LazyCollection $fileData, string $userId, string $fullFilePath): void
    {
        $cacheKey = 'import-errors-' . $userId;

        $batch = Bus::batch([])
            ->then(function (Batch $batch) use ($cacheKey, $fullFilePath, $userId) {
                $this->importNotificationService->handleValidationProcessOutcome($cacheKey, $fullFilePath, $userId);
            })->catch(function(Batch $batch) use ($userId, $cacheKey){
                Cache::delete($cacheKey);

                $this->importNotificationService->notifyInternErrorInImportProcess($userId);
            });

        $fileData->each(function (array $rowProperties, int $index) use ($batch, $cacheKey) {
            $batch->add(new FileDataValidateJob($rowProperties, $index + 1, $cacheKey));
        });

        $batch->dispatch();
    }

    /**
     * @throws \Throwable
     */
    public function initiateStoreProcess(LazyCollection $fileData, string $fullFilePath, string $userId): void
    {
        $batch = Bus::batch([])
            ->then(function (Batch $batch) use ($userId, $fullFilePath) {
                $this->importNotificationService->handleStoreProcessOutcome($userId, $fullFilePath);
            })->catch(function(Batch $batch) use($userId){
                $this->importNotificationService->notifyInternErrorInImportProcess($userId);
            });

        $fileData->each(function (array $rowProperties) use ($batch, $userId) {
            $batch->add(new FileDataStoreJob($rowProperties, $userId));
        });

        $batch->dispatch();
    }
}
