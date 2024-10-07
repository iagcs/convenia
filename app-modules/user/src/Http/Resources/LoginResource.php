<?php

namespace Modules\User\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Laravel\Sanctum\NewAccessToken;

class LoginResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $expires_at = now()->addMinutes(config('sanctum.expiration'));
        $token = $this->resource->tokens()->create([
            'name'       => 'form_token',
            'token'      => hash('sha256', $plainTextToken = \Str::random(40)),
            'abilities'  => ['*'],
            'expires_at' => $expires_at,
        ]);

        $token = new NewAccessToken($token, $token->getKey().'|'.$plainTextToken);

        return [
            'id'           => $this->resource->id,
            'name'         => $this->resource->name,
            'email'        => $this->resource->email,
            'access_token' => $token->plainTextToken,
            'expires_in'   => $expires_at->diffInSeconds(),
            'created_at'   => $this->resource->created_at->toW3cString(),
            'updated_at'   => $this->resource->updated_at->toW3cString(),
        ];
    }
}
