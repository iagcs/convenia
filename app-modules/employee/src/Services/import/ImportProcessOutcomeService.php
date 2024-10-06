<?php

namespace Modules\Employee\Services\import;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Modules\Employee\Jobs\StartStoreProcessJob;
use Modules\Employee\Notifications\ImportInternErrorNotification;
use Modules\Employee\Notifications\ImportSuccessNotification;
use Modules\Employee\Notifications\ValidationErrorNotification;
use Modules\User\Models\User;

class ImportProcessOutcomeService
{
    /**
     * @throws \JsonException
     */
    public function handleValidationProcessOutcome(string $cacheKey, string $fullFilePath, string $userId): void
    {
        if (Cache::has($cacheKey)) {
            $errors = json_decode(Cache::get($cacheKey), TRUE, 512, JSON_THROW_ON_ERROR);

            Cache::delete($cacheKey);

            Storage::delete($fullFilePath);

            User::findOrFail($userId)->notify(new ValidationErrorNotification($errors));

            return;
        }

        StartStoreProcessJob::dispatch($fullFilePath, $userId);
    }

    public function handleStoreProcessOutcome(string $userId, string $fullFilePath): void
    {
        User::findOrFail($userId)->notify(new ImportSuccessNotification);

        Storage::delete($fullFilePath);
    }

    public function notifyInternErrorInImportProcess(string $userId): void
    {
        User::findOrFail($userId)->notify(new ImportInternErrorNotification);
    }
}
