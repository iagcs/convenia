<?php

use Modules\Employee\Http\Controllers\EmployeeController;

Route::middleware('api')->group(static function () {
    Route::apiResource('employees', EmployeeController::class)
        ->whereUuid('employee');

    Route::post('/employees/import', [EmployeeController::class, 'import'])->name('employees.import');
});
