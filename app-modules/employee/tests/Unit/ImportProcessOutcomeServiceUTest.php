<?php

namespace Modules\Employee\Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Modules\Employee\Jobs\StartStoreProcessJob;
use Modules\Employee\Notifications\ImportInternErrorNotification;
use Modules\Employee\Notifications\ImportSuccessNotification;
use Modules\Employee\Notifications\ValidationErrorNotification;
use Modules\Employee\Services\import\ImportProcessOutcomeService;
use Modules\User\Models\User;
use Tests\TestCase;

uses(
    TestCase::class,
    RefreshDatabase::class,
);

describe('unit test ValidationService', function () {
    beforeEach(function () {
        $this->importService = new ImportProcessOutcomeService();
    });

    it('should test handle validation outcome validation error notification was sent.', function(){
        $cache = Cache::partialMock();

        $cache->shouldReceive('has')
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

        $cache->shouldReceive('get')
            ->once()
            ->andReturn(json_encode($errorData, JSON_THROW_ON_ERROR));

        $cache->shouldReceive('delete')
            ->once()
            ->andReturn(TRUE);

        Notification::fake();

        Queue::fake();

        $user = User::factory()->create();

        $this->importService->handleValidationProcessOutcome('test', 'test', $user->id);

        Notification::assertSentTo($user, ValidationErrorNotification::class);

        Queue::assertNothingPushed();
    });

    it('should test handle validation outcome validation success store process start.', function(){
        $cache = Cache::partialMock();

        $cache->shouldReceive('has')
            ->once()
            ->andReturn(FALSE);

        Notification::fake();

        Queue::fake();

        $user = User::factory()->create();

        $this->importService->handleValidationProcessOutcome('test', 'test', $user->id);

        Notification::assertNotSentTo($user, ValidationErrorNotification::class);

        Queue::assertPushed(StartStoreProcessJob::class);
    });

    it('should test handle store process outcome notification sent', function(){
        $user = User::factory()->create();

        Notification::fake();

        $this->importService->handleStoreProcessOutcome($user->id, 'test');

        Notification::assertSentTo($user, ImportSuccessNotification::class);
    });

    it('should test handle store process intern error notification sent', function(){
        $user = User::factory()->create();

        Notification::fake();

        $this->importService->notifyInternErrorInImportProcess($user->id);

        Notification::assertSentTo($user, ImportInternErrorNotification::class);
    });

});
