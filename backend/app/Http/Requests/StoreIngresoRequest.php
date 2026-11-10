<?php

namespace App\Http\Requests;

use App\Support\Rut;
use Closure;
use Illuminate\Foundation\Http\FormRequest;

class StoreIngresoRequest extends FormRequest
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
            'rut' => [
                'required',
                'string',
                'max:13',
                function (string $attribute, mixed $value, Closure $fail): void {
                    if (! Rut::valido((string) $value)) {
                        $fail('El RUT no es válido.');
                    }
                },
            ],
            'nombres' => ['required', 'string', 'max:65'],
            'apellidoPaterno' => ['required', 'string', 'max:30'],
            'apellidoMaterno' => ['nullable', 'string', 'max:30'],
            'telefono' => ['nullable', 'string', 'max:13'],
            'correo' => ['nullable', 'email', 'max:65'],

            'patente' => [
                'required',
                'string',
                'max:7',
                function (string $attribute, mixed $value, Closure $fail): void {
                    $patente = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', (string) $value) ?? '');
                    if (! preg_match('/^[A-Z]{2,4}\d{2,4}$/', $patente)) {
                        $fail('La patente no es válida (formato AB1234 o BBBB12).');
                    }
                },
            ],
            'idModelo' => ['required', 'integer', 'exists:modelo,idModelo'],
            'idTipoVehiculo' => ['required', 'integer', 'exists:tipovehiculo,idTipoVehiculo'],

            'idFila' => ['required', 'integer', 'exists:fila,idFila'],
            'idServicioExtra' => ['nullable', 'integer', 'exists:servicioextra,idServicioExtra'],

            'fechaInicio' => ['required', 'date'],
            'horaInicio' => ['required', 'date_format:H:i'],
            'fechaRegreso' => ['nullable', 'date', 'after_or_equal:fechaInicio'],
            'horaRegreso' => ['nullable', 'date_format:H:i'],
            'numeroVuelo' => ['nullable', 'string', 'max:12'],
            'llaves' => ['boolean'],
            'observacion' => ['nullable', 'string', 'max:500'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'rut' => Rut::normalizar((string) $this->input('rut', '')),
            'patente' => strtoupper(
                preg_replace('/[^A-Za-z0-9]/', '', (string) $this->input('patente', '')) ?? ''
            ),
        ]);
    }
}
