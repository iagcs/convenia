<?php

namespace Modules\Employee\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Modules\Employee\Http\Requests\EmployeeRequest;
use Modules\Employee\Http\Requests\ImportRequest;
use Modules\Employee\Http\Resources\EmployeeResource;
use Modules\Employee\Models\Employee;
use Modules\Employee\Services\EmployeeService;
use Modules\Employee\Services\import\ImportService;
use Symfony\Component\HttpFoundation\Response;

class EmployeeController extends Controller
{
    use AuthorizesRequests;
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->authorizeResource(Employee::class, 'employee');
    }

    public function index(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        return EmployeeResource::collection(\Auth::user()->employees);
    }

    /**
     * @throws \Spatie\LaravelData\Exceptions\InvalidDataClass
     */
    public function store(EmployeeRequest $request, EmployeeService $service): EmployeeResource
    {
        return EmployeeResource::make($service->create(\Auth::user(), $request->getData()));
    }

    /**
     * @throws \Spatie\LaravelData\Exceptions\InvalidDataClass
     */
    public function update(EmployeeRequest $request, Employee $employee, EmployeeService $service): EmployeeResource
    {
        return EmployeeResource::make($service->update($employee, $request->getData()));
    }

    public function show(Employee $employee): EmployeeResource
    {
        return EmployeeResource::make($employee);
    }

    public function destroy(Employee $employee, EmployeeService $service): JsonResponse
    {
        $service->destroy($employee);

        return new JsonResponse("Funcionario deletado.", Response::HTTP_NO_CONTENT);
    }

    /**
     * @throws \Throwable
     */
    public function import(ImportRequest $request, ImportService $service): JsonResponse
    {
        $file = Storage::drive('local')->putFileAs('imports', $request->file, 'import-' . \Auth::user()->email . '.csv');

        $service->import(\Auth::user(), $file);

		return new JsonResponse(
			"O processo de importação foi iniciado. Você receberá atualizações sobre o andamento diretamente no seu e-mail.",
			Response::HTTP_ACCEPTED
		);
    }
}
