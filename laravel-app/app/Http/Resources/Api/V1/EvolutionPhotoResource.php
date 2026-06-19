<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\EvolutionPhoto */
class EvolutionPhotoResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'registered_date' => $this->registered_date,
            'weight_kg' => $this->weight_kg,
            'media_url' => url("/api/v1/media/evolution/{$this->id}"),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
