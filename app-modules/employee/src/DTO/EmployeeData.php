<?php

namespace Modules\Employee\DTO;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class EmployeeData extends Data
{
    public function __construct(
        public string | Optional $id,
        public string | Optional $name,
        public string | Optional $email,
        public string | Optional $cpf,
        public string | Optional $city,
        public string | Optional $state,
    ){}
}
