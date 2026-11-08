<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'contrasena_actual' => ['required', 'string'],
            'contrasena' => ['required', 'string', 'min:8', 'max:255', 'confirmed'],
        ];
    }
}
