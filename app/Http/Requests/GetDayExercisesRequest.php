<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetDayExercisesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'dayNumber' => 'required|integer|min:1',
        ];
    }
}
