<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateExerciseDetailsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'setNumber' => 'required|integer|min:1',
            'reps' => 'nullable|integer|min:1',
            'weightKg' => 'nullable|numeric|min:0',
            'duration' => 'nullable|integer|min:1',
            'reset_duration' => 'nullable|integer|min:1',
            'notes' => 'nullable|string|max:255',
        ];
    }
}
