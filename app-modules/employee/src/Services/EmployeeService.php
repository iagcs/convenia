<?php

namespace Modules\Employee\Services;

use Modules\User\Models\User;
use Modules\Employee\Models\Employee;
use Modules\Employee\DTO\EmployeeData;

class EmployeeService
{
    public function create(User $user, EmployeeData $employeeData): Employee
    {
        $employee = new Employee($employeeData->toArray());

        $employee->user()->associate($user);

        $employee->save();

        return $employee;
    }

    public function update(Employee $employee, EmployeeData $employeeData): Employee
    {
        $employee->update($employeeData->toArray());

        return $employee;
    }

    public function show(Employee $employee): Employee
    {
        return $employee;
    }

    public function destroy(Employee $employee): void
    {
        $employee->delete();
    }

}
