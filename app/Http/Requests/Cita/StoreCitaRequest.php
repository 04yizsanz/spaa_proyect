<?php

namespace App\Http\Requests\Cita;

use Illuminate\Foundation\Http\FormRequest;

class StoreCitaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'codigo_cita' => ['required', 'integer', 'unique:citas,codigo_cita'],
            'fecha' => ['required', 'date'],
            'hora' => ['required', 'date_format:H:i'],
            'estado' => ['nullable', 'in:pendiente,confirmada,completada,cancelada'],
            'cliente_id' => ['required', 'integer', 'exists:clientes,id'],
            'empleado_id' => ['required', 'integer', 'exists:empleados,empleado_id'],
            'servicio_id' => ['required', 'integer', 'exists:servicios,servicio_id'],
        ];
    }

    public function messages(): array
    {
        return [
            'codigo_cita.required' => 'El código de la cita es obligatorio.',
            'codigo_cita.integer' => 'El código de la cita debe ser un número entero.',
            'codigo_cita.unique' => 'Ya existe una cita con ese código.',

            'fecha.required' => 'La fecha es obligatoria.',
            'fecha.date' => 'La fecha debe ser válida.',

            'hora.required' => 'La hora es obligatoria.',
            'hora.date_format' => 'La hora debe tener el formato HH:MM.',

            'estado.in' => 'El estado de la cita no es válido.',

            'cliente_id.required' => 'El cliente es obligatorio.',
            'cliente_id.integer' => 'El cliente debe ser válido.',
            'cliente_id.exists' => 'El cliente seleccionado no existe.',

            'empleado_id.required' => 'El empleado es obligatorio.',
            'empleado_id.integer' => 'El empleado debe ser válido.',
            'empleado_id.exists' => 'El empleado seleccionado no existe.',

            'servicio_id.required' => 'El servicio es obligatorio.',
            'servicio_id.integer' => 'El servicio debe ser válido.',
            'servicio_id.exists' => 'El servicio seleccionado no existe.',
        ];
    }
}