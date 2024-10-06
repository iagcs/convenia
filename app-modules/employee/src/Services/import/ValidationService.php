<?php

namespace Modules\Employee\Services\import;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ValidationService
{
    public function validateFileHeaders(array $headers): void
    {
        $missingHeaders = array_diff(ImportService::REQUIRED_HEADERS, $headers);
        abort_if(!empty($missingHeaders), 400, 'This file must have ' . implode(',', $missingHeaders) . '.');

        $unknownHeaders = array_diff($headers, ImportService::REQUIRED_HEADERS);
        abort_if(!empty($unknownHeaders), 400, 'The fields ' . implode(',', $unknownHeaders) . ' do not exist in the employee table.');
    }

    /**
     * @throws \Illuminate\Contracts\Cache\LockTimeoutException
     */
    public function validateRowData(array $rowData, int $index, string $cacheKey): void
    {
        $validator = Validator::make($rowData, [
            'name'  => 'bail|required|string',
            'email' => [
                'bail',
                'required',
                'email',
                Rule::unique('employees', 'email'),
            ],
            'cpf'   => 'bail|required|string',
            'city'  => 'bail|required|string',
            'state' => 'bail|required|string',
        ]);

        if ($validator->fails()) {
            $this->storeValidationErrors($validator->errors()->toArray(), $index, $cacheKey);
        }
    }

    /**
     * @throws \Illuminate\Contracts\Cache\LockTimeoutException
     * Fiz o lock do cache para evitar sobrescritas (Atomic Locks), ja que o processo de salvamento eh assincrono
     */
    public function storeValidationErrors(array $errors, int $index, string $cacheKey): void
    {
        Cache::lock('validation_errors_lock', 2)->block(2, function () use ($cacheKey, $index, $errors) {
            $value = [$index => \Arr::collapse($errors)];

            if (Cache::has($cacheKey)) {
                $currentValue = json_decode(Cache::get($cacheKey), TRUE, 512, JSON_THROW_ON_ERROR);
                $newValue = $currentValue + $value;
            } else {
                $newValue = $value;
            }

            Cache::put($cacheKey, json_encode($newValue, JSON_THROW_ON_ERROR), now()->addMinutes(60));
        });
    }
}
