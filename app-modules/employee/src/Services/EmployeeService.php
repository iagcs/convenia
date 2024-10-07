<?php

namespace Modules\Employee\Services;

use Modules\User\Models\User;
use Modules\Employee\Models\Employee;
use Modules\Employee\DTO\EmployeeData;

class EmployeeService
{
    /**
     * @throws \Spatie\LaravelData\Exceptions\InvalidDataClass
     */
    public function create(User $user, EmployeeData $employeeData): EmployeeData
    {
        $employee = new Employee($employeeData->toArray());

        $employee->user()->associate($user);

        $employee->save();

        return $employee->getData();
    }

    /**
     * @throws \Spatie\LaravelData\Exceptions\InvalidDataClass
     */
    public function update(Employee $employee, EmployeeData $employeeData): EmployeeData
    {
        $employee->update($employeeData->toArray());

        return $employee->getData();
    }

    /**
     * @throws \Spatie\LaravelData\Exceptions\InvalidDataClass
     */
    public function show(Employee $employee): Employee
    {
        return $employee->getData();
    }

    public function destroy(Employee $employee): void
    {
        $employee->delete();
    }

}
