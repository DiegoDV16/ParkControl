<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUsuarioRequest extends FormRequest
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
            'idRol' => ['required', 'integer', Rule::exists('rol', 'idRol')],
            'rut' => ['required', 'string', 'max:13', Rule::unique('usuario', 'rut')],
            'nombres' => ['required', 'string', 'max:65'],
            'apellidoPaterno' => ['required', 'string', 'max:30'],
            'apellidoMaterno' => ['nullable', 'string', 'max:30'],
            'correo' => ['required', 'email', 'max:65', Rule::unique('usuario', 'correo')],
            'contrasena' => ['required', 'string', 'min:8', 'max:255'],
        ];
    }
}
