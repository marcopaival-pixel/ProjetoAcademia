<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'birth_date' => ['nullable', 'date_format:Y-m-d'],
            'sex' => ['required', 'in:M,F,O'],
            'height_cm' => ['nullable', 'integer', 'between:50,260'],
            'activity_level' => ['required', 'in:sedentary,light,moderate,active,very_active'],
            'climate' => ['required', 'in:cold,moderate,hot'],
            'goal' => ['required', 'in:lose,lose_aggressive,recomp,maintain,gain,performance'],
            'current_weight_kg' => ['nullable', 'numeric', 'between:20,500'],
            'target_weight_kg' => ['nullable', 'numeric', 'between:20,500'],
            'training_days_per_week' => ['nullable', 'in:1-2,3-4,5-6,all'],
            'daily_calorie_target' => ['nullable', 'integer', 'between:500,20000'],
            'protein_target_g' => ['nullable', 'numeric', 'between:0,600'],
            'carbs_target_g' => ['nullable', 'numeric', 'between:0,600'],
            'fat_target_g' => ['nullable', 'numeric', 'between:0,600'],
            'water_target_ml' => ['nullable', 'integer', 'between:500,10000'],
        ];
    }

    protected function prepareForValidation(): void
    {
        // Limpeza de campos para garantir que vírgulas sejam tratadas como pontos em decimais
        if ($this->has('current_weight_kg')) {
            $this->merge(['current_weight_kg' => str_replace(',', '.', $this->current_weight_kg)]);
        }
        if ($this->has('target_weight_kg')) {
            $this->merge(['target_weight_kg' => str_replace(',', '.', $this->target_weight_kg)]);
        }
        if ($this->has('protein_target_g')) {
            $this->merge(['protein_target_g' => str_replace(',', '.', $this->protein_target_g)]);
        }
        if ($this->has('carbs_target_g')) {
            $this->merge(['carbs_target_g' => str_replace(',', '.', $this->carbs_target_g)]);
        }
        if ($this->has('fat_target_g')) {
            $this->merge(['fat_target_g' => str_replace(',', '.', $this->fat_target_g)]);
        }
    }
}
