<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RequestServiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'status' => $this->status,
            'created_at' => $this->created_at,
            
           'service' => $this->whenLoaded('service', function() {
                return [
                    'id' => $this->service->id,
                    'name' => $this->service->name,
                ];
            }),

            'user' => $this->whenLoaded('user', function() {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                ];
            }),
        ];
    }
}
