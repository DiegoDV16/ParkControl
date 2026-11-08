<?php

namespace App\Http\Requests;

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
            'rut' => ['required', 'string', 'max:13'],
            'nombres' => ['required', 'string', 'max:65'],
            'apellidoPaterno' => ['required', 'string', 'max:30'],
            'apellidoMaterno' => ['nullable', 'string', 'max:30'],
            'telefono' => ['nullable', 'string', 'max:13'],
            'correo' => ['nullable', 'email', 'max:65'],

            'patente' => ['required', 'string', 'max:7'],
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
}
