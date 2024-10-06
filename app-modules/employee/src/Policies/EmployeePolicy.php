<?php

namespace Modules\Employee\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Modules\Employee\Models\Employee;
use Modules\User\Models\User;

class EmployeePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): Response
    {
        return $this->allow();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Employee $employee): Response
    {
        return $user->id === $employee->user_id
            ? $this->allow()
            : $this->deny('Voce nao pode visualizar usuario que nao lidera.');
    }


    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Employee $employee): Response
    {
        return $user->id === $employee->user_id
            ? $this->allow()
            : $this->deny('Voce nao pode atualizar usuario que nao lidera.');
    }

    public function create(User $user): Response
    {
        return $this->allow();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Employee $employee): Response
    {
        return $user->id === $employee->user_id
            ? $this->allow()
            : $this->deny('Voce nao pode deletar usuario que nao lidera.');
    }
}
