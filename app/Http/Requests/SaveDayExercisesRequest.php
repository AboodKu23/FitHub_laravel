<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveDayExercisesRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'days' => 'required|array|min:1',
            'days.*.dayNumber' => 'required|integer|min:1|max:60',
            'days.*.exercises' => 'nullable|array',
            'days.*.exercises.*.exercise_id' => 'required|exists:exercises,id',
        ];
    }
}
