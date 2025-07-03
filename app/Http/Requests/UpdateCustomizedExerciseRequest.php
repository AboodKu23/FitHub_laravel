<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCustomizedExerciseRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'setNumber'       => 'nullable|integer|min:1',
            'resp'            => 'nullable|integer|min:1',
            'weightKg'        => 'nullable|numeric|min:0',
            'duration'        => 'nullable|integer|min:1',
            'reset_duration'  => 'nullable|integer|min:0',
            'notes'           => 'nullable|string|max:1000',
            'order_in_day'    => 'nullable|integer|min:1',
        ];
    }
}
