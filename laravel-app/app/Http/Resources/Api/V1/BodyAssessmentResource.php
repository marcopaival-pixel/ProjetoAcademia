<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\BodyAssessment */
class BodyAssessmentResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'assessment_date' => $this->assessment_date?->toDateString(),
            'weight_kg' => $this->weight_kg,
            'bf_percent' => $this->bf_percent,
            'muscle_percent' => $this->muscle_percent,
            'neck' => $this->neck,
            'chest' => $this->chest,
            'waist' => $this->waist,
            'abdomen' => $this->abdomen,
            'hips' => $this->hips,
            'notes' => $this->notes,
            'status' => $this->status,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
