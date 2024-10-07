<?php

namespace Modules\Employee\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Employee\DTO\EmployeeData;
use Modules\User\Models\User;
use Spatie\LaravelData\WithData;

class Employee extends Model
{
    use HasUuids, HasFactory, WithData;

    protected string $dataClass = EmployeeData::class;

    protected $fillable = [
        'name',
        'email',
        'cpf',
        'city',
        'state'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
