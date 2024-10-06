<?php

namespace Modules\Employee\Services\import;

use Illuminate\Support\Facades\Storage;
use Modules\User\Models\User;
use Spatie\SimpleExcel\SimpleExcelReader;

class ImportService
{
    public const REQUIRED_HEADERS = ['name', 'email', 'cpf', 'state', 'city'];

    public function __construct(
        private readonly ValidationService $validationService,
        private readonly BatchProcessingService $batchProcessingService
    ){}

    /**
     * @throws \Throwable
     */
    public function import(User $user, string $filePath): void
    {
        $fullFilePath = Storage::path($filePath);
        $fileHandler = SimpleExcelReader::create($fullFilePath);

        $this->validationService->validateFileHeaders($fileHandler->getHeaders());

        $this->batchProcessingService->initiateValidationProcess($fileHandler->getRows(), $user->id, $fullFilePath);
    }

}
