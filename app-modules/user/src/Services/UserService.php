<?php

namespace Modules\User\services;

use Modules\User\DTO\LoginData;
use Modules\User\Models\User;

class UserService
{
    public function login(LoginData $loginData): User
    {
        return User::query()
            ->where('email', $loginData->email)
            ->where('password', $loginData->password)
            ->firstOr(function(){
                    abort(400, "Senha incorreta.");
            });
    }
}
