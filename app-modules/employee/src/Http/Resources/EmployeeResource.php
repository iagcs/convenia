<?php

namespace Modules\Employee\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read \Modules\Employee\DTO\EmployeeData $resource
 */
class EmployeeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->resource->id,
            'name'       => $this->resource->name,
            'email'      => $this->resource->email,
            'cpf'        => $this->resource->cpf,
            'city'       => $this->resource->city,
            'state'      => $this->resource->state,
            'created_at' => $this->resource->created_at,
            'updated_at' => $this->resource->updated_at,
            'manager'    => $this->resource->user,
        ];
    }
}
