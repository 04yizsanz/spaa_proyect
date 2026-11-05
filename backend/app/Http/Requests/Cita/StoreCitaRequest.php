<?php

namespace App\Http\Requests\Cita;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validación de citas nuevas (API y web).
 *
 * La autorización la aplican la ruta (can:create) y CitaService/CitaPolicy:
 * quien no gestiona citas solo agenda a su propio nombre, como 'pendiente',
 * y el código de la cita lo asigna el servidor.
 */
class StoreCitaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $gestionaCitas = (bool) $this->user()?->tienePermiso('citas.gestionar');

        return [
            'codigo_cita' => ['nullable', 'integer', 'min:1', 'unique:citas,codigo_cita'],
            'fecha' => ['required', 'date', 'after_or_equal:today'],
            'hora' => ['required', 'date_format:H:i'],
            'estado' => ['nullable', 'in:pendiente,confirmada,completada,cancelada'],
            'cliente_id' => [
                $gestionaCitas ? 'required' : 'nullable',
                'integer',
                Rule::exists('clientes', 'cliente_id')->whereNull('deleted_at'),
            ],
            'empleado_id' => [
                'required',
                'integer',
                Rule::exists('empleados', 'empleado_id')->where('disponibilidad', true)->whereNull('deleted_at'),
            ],
            'servicio_id' => [
                'required',
                'integer',
                Rule::exists('servicios', 'servicio_id')->where('activo', true)->whereNull('deleted_at'),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'codigo_cita.integer' => 'El código de la cita debe ser un número entero.',
            'codigo_cita.min' => 'El código de la cita debe ser un número positivo.',
            'codigo_cita.unique' => 'Ya existe una cita con ese código.',

            'fecha.required' => 'La fecha es obligatoria.',
            'fecha.date' => 'La fecha debe ser válida.',
            'fecha.after_or_equal' => 'La fecha de la cita no puede ser anterior a hoy.',

            'hora.required' => 'La hora es obligatoria.',
            'hora.date_format' => 'La hora debe tener el formato HH:MM.',

            'estado.in' => 'El estado de la cita no es válido.',

            'cliente_id.required' => 'El cliente es obligatorio.',
            'cliente_id.integer' => 'El cliente debe ser válido.',
            'cliente_id.exists' => 'El cliente seleccionado no existe.',

            'empleado_id.required' => 'El empleado es obligatorio.',
            'empleado_id.integer' => 'El empleado debe ser válido.',
            'empleado_id.exists' => 'El empleado seleccionado no existe o no está disponible.',

            'servicio_id.required' => 'El servicio es obligatorio.',
            'servicio_id.integer' => 'El servicio debe ser válido.',
            'servicio_id.exists' => 'El servicio seleccionado no existe o no está activo.',
        ];
    }
}
