<?php

namespace Modules\Employee\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Modules\Employee\Jobs\FileDataStoreJob;
use Modules\Employee\Jobs\FileDataValidateJob;
use Modules\Employee\Models\Employee;
use Modules\Employee\Services\EmployeeService;
use Modules\Employee\Services\import\ImportService;
use Modules\Employee\Services\import\ValidationService;
use Modules\User\Models\User;
use Tests\TestCase;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\delete;
use function Pest\Laravel\get;
use function Pest\Laravel\post;
use function Pest\Laravel\put;
use function Pest\Laravel\withoutExceptionHandling;

uses(
    TestCase::class,
    RefreshDatabase::class,
);

describe('Testa API de Employee', function(){

    it('should create employee success', function(){
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $employee = Employee::factory()->make();

        post(route('employees.store'), $employee->toArray())
            ->assertOk();

        assertDatabaseCount('employees', 1);
        assertDatabaseHas('employees', $employee->toArray());
    });

    it('should update employee success', function(){
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $employee = Employee::factory()->for($user)->create();

        $updatedData = [
            'name' => 'Updated Employee Name',
            'email' => 'updated.email@example.com',
        ];

        put(route('employees.update', $employee->id), $updatedData)
            ->assertOk();

        assertDatabaseCount('employees', 1);
        assertDatabaseHas('employees', $updatedData);
    });

    it('should update employee fails forbidden access', function(){
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $employee = Employee::factory()->for(User::factory())->create();

        $updatedData = [
            'name' => 'Updated Employee Name',
            'email' => 'updated.email@example.com',
        ];

        put(route('employees.update', $employee->id), $updatedData)
            ->assertForbidden();
    });

    it('updates employee fail not found', function () {
        $user = User::factory()->create();
        $newName = fake()->name;

        Sanctum::actingAs($user);

        put(route('employees.update', fake()->uuid()), [
            'name' => $newName,
        ])->assertNotFound();
    });

    it('should show employee success', function(){
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $employee = Employee::factory()->for($user)->create();


        get(route('employees.show', $employee->id))
            ->assertOk()
            ->assertJsonFragment(\Arr::only($employee->toArray(), ['name', 'email', 'cpf', 'city', 'state']));
    });

    it('should show employee fails forbidden access', function(){
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $employee = Employee::factory()->for(User::factory())->create();

        get(route('employees.show', $employee->id))
            ->assertForbidden();
    });

    it('show employee fail not found', function () {
        $user = User::factory()->create();
        $newName = fake()->name;

        Sanctum::actingAs($user);

        get(route('employees.show', fake()->uuid()), [
            'name' => $newName,
        ])->assertNotFound();
    });

    it('should delete employee success', function(){
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $employee = Employee::factory()->for($user)->create();

        delete(route('employees.destroy', $employee->id))
            ->assertNoContent();

        assertDatabaseCount('employees', 0);
        assertDatabaseMissing('employees', ['id' => $employee->id]);
    });

    it('should delete employee fails forbidden access', function(){
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $employee = Employee::factory()->for(User::factory())->create();

        delete(route('employees.destroy', $employee->id))
            ->assertForbidden();
    });

    it('delete employee fail not found', function () {
        $user = User::factory()->create();
        $newName = fake()->name;

        Sanctum::actingAs($user);

        delete(route('employees.show', fake()->uuid()), [
            'name' => $newName,
        ])->assertNotFound();
    });

	it('should test import validation headers missing', function(){
		Storage::fake('local');
		Cache::flush();

		$user = User::factory()->create();
		Sanctum::actingAs($user);

		$csvContent = "name,email,cpf,state\n";

		$filePath = 'imports/employees.csv';
		Storage::put($filePath, $csvContent);

		$file = UploadedFile::fake()->createWithContent('employees.csv', $csvContent);

		withoutExceptionHandling();

		post(route('employees.import'), [
			'file' => $file
		])->assertStatus(400);
	})->throws(\Exception::class, "This file must have city.");

	it('should test import validation headers unknown', function(){
		Storage::fake('local');
		Cache::flush();

		$user = User::factory()->create();
		Sanctum::actingAs($user);

		$csvContent = "name,email,cpf,city,state,test\n";

		$filePath = 'imports/employees.csv';
		Storage::put($filePath, $csvContent);

		$file = UploadedFile::fake()->createWithContent('employees.csv', $csvContent);

		withoutExceptionHandling();

		post(route('employees.import'), [
			'file' => $file
		])->assertStatus(400);
	})->throws(\Exception::class, "The fields test do not exist in the employee table.");

    it(/**
     * @throws \JsonException
     */ 'should test import validation error was save in cache', function(){
        Storage::fake('local');
        Cache::flush();

        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $employees = collect([
            Employee::factory()->make(['email' => 'invalid-email', 'name' => 'John Doe']), // Erro: email inválido
            Employee::factory()->make(['email' => 'jane@example.com', 'name' => '']), // Erro: nome vazio
            Employee::factory()->make(['name' => 'Jane Doe', 'email' => 'jane.doe@example.com']),
            Employee::factory()->make(['name' => 'Alex Smith', 'email' => 'alex@example.com']),
            Employee::factory()->make(['name' => 'Sam Taylor', 'email' => 'sam.taylor@example.com']),
        ]);

        $csvContent = "name,email,cpf,city,state\n";
        foreach ($employees as $employee) {
            $csvContent .= "{$employee->name},{$employee->email},{$employee->cpf},{$employee->city},{$employee->state}\n";
        }

        $filePath = 'imports/employees.csv';
        Storage::put($filePath, $csvContent);

        $file = UploadedFile::fake()->createWithContent('employees.csv', $csvContent);

        Queue::fake();

        post(route('employees.import'), [
            'file' => $file
        ])->assertAccepted();

        Queue::assertPushed(FileDataValidateJob::class, 5);

        foreach ($employees as $index => $employee) {
            $job = new FileDataValidateJob($employee->toArray(), $index, 'import-errors-'.$user->id);
            $job->handle(app(ValidationService::class));
        }

        $cacheKey = 'import-errors-' . $user->id;
        $errors = json_decode(Cache::get($cacheKey), true, 512, JSON_THROW_ON_ERROR);

        expect($errors)->toBeArray()
            ->and($errors)->toHaveCount(2)
            ->and($errors[0][0])->toBe('The email field must be a valid email address.')
            ->and($errors[1][0])->toBe('The name field is required.');
    });

    it('should import each employee data correctly', function () {
        $user = User::factory()->create();

        $employees = Employee::factory(3)->make();

        foreach ($employees as $employee) {
            $importJob = new FileDataStoreJob($employee->toArray(), $user->id);
            $importJob->handle(app(EmployeeService::class));
        }

        assertDatabaseCount('employees', 3);
        foreach ($employees as $employee) {
            assertDatabaseHas('employees', $employee->toArray());
        }
    });
});
