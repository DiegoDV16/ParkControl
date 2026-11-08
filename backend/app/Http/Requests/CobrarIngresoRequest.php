<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CobrarIngresoRequest extends FormRequest
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
            'fechaRegreso' => ['required', 'date'],
            'horaRegreso' => ['required', 'date_format:H:i'],
            'metodo' => ['required', 'in:efectivo,debito,credito,transferencia'],
            'montoRecibido' => ['nullable', 'numeric', 'min:0'],
            'referencia' => ['nullable', 'string', 'max:50'],
        ];
    }
}
