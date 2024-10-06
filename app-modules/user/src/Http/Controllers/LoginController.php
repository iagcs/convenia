<?php

namespace Modules\User\Http\Controllers;

use Modules\User\Http\Requests\LoginRequest;
use Modules\User\Http\Resources\LoginResource;
use Modules\User\services\UserService;

readonly class LoginController
{
    public function __construct(private UserService $service) {}

    /**
     * @throws \Spatie\LaravelData\Exceptions\InvalidDataClass
     */
    public function __invoke(LoginRequest $request): LoginResource
    {
        return LoginResource::make($this->service->login($request->getData()));
    }
}
