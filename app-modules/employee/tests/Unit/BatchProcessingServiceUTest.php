<?php

namespace Modules\Employee\Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\LazyCollection;
use Illuminate\Support\Testing\Fakes\PendingBatchFake;
use Modules\Employee\Jobs\StartStoreProcessJob;
use Modules\Employee\Models\Employee;
use Modules\Employee\Notifications\ImportInternErrorNotification;
use Modules\Employee\Notifications\ImportSuccessNotification;
use Modules\Employee\Notifications\ValidationErrorNotification;
use Modules\Employee\Services\import\BatchProcessingService;
use Modules\Employee\Services\import\ImportProcessOutcomeService;
use Modules\User\Models\User;
use Tests\TestCase;
use Mockery;

uses(
    TestCase::class,
    RefreshDatabase::class,
);

describe('unit test BatchProcessingService', function () {
    beforeEach(function () {
        $this->importService = Mockery::mock(ImportProcessOutcomeService::class);

        $this->batchService = new BatchProcessingService($this->importService);
    });

    it('should test validation process success.', function(){
       Bus::fake();

       $employees = LazyCollection::make(Employee::factory(5)->make()->toArray());

       $this->batchService->initiateValidationProcess($employees, fake()->uuid, 'test');

       Bus::assertBatched(function(PendingBatchFake $batch){
           return $batch->jobs->count() === 5;
       });
    });

    it('should test store process success.', function(){
        Bus::fake();

        $employees = LazyCollection::make(Employee::factory(5)->make()->toArray());

        $this->batchService->initiateStoreProcess($employees, fake()->uuid, 'test');

        Bus::assertBatched(function(PendingBatchFake $batch){
            return $batch->jobs->count() === 5;
        });
    });
});
