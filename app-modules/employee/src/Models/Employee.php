<?php

namespace Modules\Employee\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\User\Models\User;

class Employee extends Model
{
    use HasUuids, HasFactory;

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
