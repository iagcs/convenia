<?php

namespace Modules\Employee\Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Modules\Employee\Models\Employee;
use Modules\Employee\Services\import\ValidationService;
use Mockery;
use Tests\TestCase;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertFalse;
use function PHPUnit\Framework\assertTrue;

uses(
    TestCase::class,
    RefreshDatabase::class,
);

describe('unit test ValidationService', function () {
    beforeEach(function () {
        $this->validationService = new ValidationService();
    });

    it('validates file headers correctly', function () {
        $headers = ['name', 'email', 'cpf', 'state', 'city'];

        $this->validationService->validateFileHeaders($headers);

        $this->assertTrue(TRUE);
    });

    it('throws an exception for missing headers', function () {
        $headers = ['name', 'email', 'cpf'];

        $this->validationService->validateFileHeaders($headers);
    })->throws(\Exception::class, 'This file must have state,city.');

    it('throws an exception for unknown headers', function () {
        $headers = ['name', 'email', 'cpf', 'state', 'city', 'unknown'];

        $this->validationService->validateFileHeaders($headers);
    })->throws(\Exception::class, 'The fields unknown do not exist in the employee table.');

    it('should test validation row data error saved on cache', function () {
        Cache::delete('test');

        $validatorMock = Validator::partialMock();

        $validatorMock->shouldReceive('make')
            ->once()
            ->andReturn($validatorMock);

        $validatorMock->shouldReceive('fails')
            ->once()
            ->andReturn(TRUE);

        $errorData = [
            "email" => [
                0 => "The email field must be a valid email address.",
            ],
            "cpf"   => [
                0 => "The cpf field is required.",
            ],
        ];

        $validatorMock->shouldReceive('errors')
            ->once()
            ->andReturn(collect($errorData));

        $index = 10;

        $this->validationService->validateRowData([], $index, 'test');

        assertTrue(Cache::has('test'));
        assertEquals(Cache::get('test'), json_encode([$index => \Arr::collapse($errorData)], JSON_THROW_ON_ERROR));

        Cache::delete('test');
    });

    it('should test validation row data dont has error', function () {
        Cache::delete('test');

        $validatorMock = Validator::partialMock();

        $validatorMock->shouldReceive('make')
            ->once()
            ->andReturn($validatorMock);

        $validatorMock->shouldReceive('fails')
            ->once()
            ->andReturn(FALSE);

        $index = 10;

        $this->validationService->validateRowData([], $index, 'test');

        assertFalse(Cache::has('test'));

        Cache::delete('test');
    });
});
