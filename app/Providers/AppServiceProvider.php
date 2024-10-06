<?php

namespace App\Providers;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Modules\Employee\Models\Employee;
use Modules\Employee\Policies\EmployeePolicy;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application Services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application Services.
     */
    public function boot(): void
    {
        JsonResource::withoutWrapping();

        Gate::policy(Employee::class, EmployeePolicy::class);
    }
}
