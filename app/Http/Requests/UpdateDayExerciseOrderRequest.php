<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDayExerciseOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'dayNumber' => 'required|integer|min:1|max:60',
            'exercises' => 'required|array|min:1',
            'exercises.*.id' => 'required|exists:training_plan_exercises,id',
            'exercises.*.orderInDay' => 'required|integer|min:1',
        ];
    }
}
