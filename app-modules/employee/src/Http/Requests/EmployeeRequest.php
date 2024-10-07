<?php

namespace Modules\Employee\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Employee\DTO\EmployeeData;
use Spatie\LaravelData\WithData;

class EmployeeRequest extends FormRequest
{
    use WithData;

    protected string $dataClass = EmployeeData::class;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'  => [
                'bail',
                Rule::requiredIf(fn() => $this->route() === route('employees.store')),
                'string'
            ],
            'email' => [
                'bail',
                Rule::requiredIf(fn() => $this->route() === route('employees.store')),
                'email',
                Rule::unique('employees', 'email'),
            ],
            'cpf'   => [
                'bail',
                Rule::requiredIf(fn() => $this->route() === route('employees.store')),
                'string'
            ],
            'city'  => [
                'bail',
                Rule::requiredIf(fn() => $this->route() === route('employees.store')),
                'string'
            ],
            'state' => [
                'bail',
                Rule::requiredIf(fn() => $this->route() === route('employees.store')),
                'string'
            ],
        ];
    }
}
