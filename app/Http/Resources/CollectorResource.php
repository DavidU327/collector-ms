<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CollectorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'collector' => [
                'id' => $this->id,
                'user' => [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'phone' => $this->user->phone,
                    'identification' => $this->user->identification,
                    'type_identification' => [
                        'id' => $this->user->typeIdentification->id,
                        'name' => $this->user->typeIdentification->name,
                    ],
                    'rol' => [
                        'id' => $this->user->rol->id,
                        'name' => $this->user->rol->name,
                    ],
                    'email' => $this->user->email,
                    'image' => $this->user->image,
                ],
                'identification_document' => $this->identification_document,
                'driving_license_document' => $this->driving_license_document,
                'deleted_at' => $this->deleted_at,
                'state' => [
                    'id' => $this->state->id,
                    'name' => $this->state->name,
                    'color' => $this->state->color,
                ]
            ],
        ];
    }
}
