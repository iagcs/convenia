<?php

namespace Modules\User\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\User\Models\User;
use Tests\TestCase;
use function Pest\Laravel\post;
use function Pest\Laravel\withoutExceptionHandling;

uses(
    TestCase::class,
    RefreshDatabase::class,
);

test('Login API Test success', function () {
    $user = User::factory()->create();

    post(route('auth.login'), [
        'email' => $user->email,
        'password' => $user->password
    ])->assertOk();
});

test('Login API Test fails', function () {
    $user = User::factory()->create();

    withoutExceptionHandling();

    post(route('auth.login'), [
        'email' => $user->email,
        'password' => fake()->password
    ])->assertBadRequest();

})->throws(\Exception::class, "Senha incorreta.");
