<?php

namespace App\Http\Requests\Cita;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCitaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'codigo_cita' => [
                'sometimes',
                'integer',
                'unique:citas,codigo_cita,' . $this->route('id') . ',citas_id',
            ],

            'fecha' => ['sometimes', 'date'],

            'hora' => ['sometimes', 'date_format:H:i'],

            'estado' => [
                'sometimes',
                'in:pendiente,confirmada,completada,cancelada',
            ],

            'cliente_id' => [
                'sometimes',
                'integer',
                'exists:clientes,id',
            ],

            'empleado_id' => [
                'sometimes',
                'integer',
                'exists:empleados,empleado_id',
            ],

            'servicio_id' => [
                'sometimes',
                'integer',
                'exists:servicios,servicio_id',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'codigo_cita.integer' => 'El código de la cita debe ser un número entero.',
            'codigo_cita.unique' => 'Ya existe una cita con ese código.',

            'fecha.date' => 'La fecha debe ser válida.',

            'hora.date_format' => 'La hora debe tener el formato HH:MM.',

            'estado.in' => 'El estado de la cita no es válido.',

            'cliente_id.integer' => 'El cliente debe ser válido.',
            'cliente_id.exists' => 'El cliente seleccionado no existe.',

            'empleado_id.integer' => 'El empleado debe ser válido.',
            'empleado_id.exists' => 'El empleado seleccionado no existe.',

            'servicio_id.integer' => 'El servicio debe ser válido.',
            'servicio_id.exists' => 'El servicio seleccionado no existe.',
        ];
    }
}