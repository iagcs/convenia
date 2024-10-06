<?php

namespace Modules\User\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rule;
use Modules\User\DTO\LoginData;
use Spatie\LaravelData\WithData;

class LoginRequest extends FormRequest
{
    use WithData;

    protected $dataClass = LoginData::class;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => [
                'bail',
                'required',
                'email',
                Rule::exists('users', 'email')
            ],
            'password' => [
                'bail',
                'required',
                'string'
            ]
        ];
    }
}
