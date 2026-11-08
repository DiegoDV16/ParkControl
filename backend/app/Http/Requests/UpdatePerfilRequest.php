<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePerfilRequest extends FormRequest
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
        $id = $this->user()->idUsuario;

        return [
            'nombres' => ['required', 'string', 'max:65'],
            'apellidoPaterno' => ['required', 'string', 'max:30'],
            'apellidoMaterno' => ['nullable', 'string', 'max:30'],
            'correo' => [
                'required',
                'email',
                'max:65',
                Rule::unique('usuario', 'correo')->ignore($id, 'idUsuario'),
            ],
        ];
    }
}
