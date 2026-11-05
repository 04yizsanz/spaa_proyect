<?php

namespace App\Http\Requests\Cita;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validación de cambios en citas (API y web).
 *
 * La autorización la aplican CitaPolicy y CitaService: quien no gestiona
 * citas no puede reasignarlas, cambiar su código ni asignar estados
 * distintos de 'cancelada'.
 */
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
                'min:1',
                Rule::unique('citas', 'codigo_cita')->ignore($this->citaId(), 'citas_id'),
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
                Rule::exists('clientes', 'cliente_id')->whereNull('deleted_at'),
            ],

            'empleado_id' => [
                'sometimes',
                'integer',
                Rule::exists('empleados', 'empleado_id')->whereNull('deleted_at'),
            ],

            'servicio_id' => [
                'sometimes',
                'integer',
                Rule::exists('servicios', 'servicio_id')->whereNull('deleted_at'),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'codigo_cita.integer' => 'El código de la cita debe ser un número entero.',
            'codigo_cita.min' => 'El código de la cita debe ser un número positivo.',
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

    /**
     * Id de la cita en edición: {cita} en la API (apiResource) o {id} en la web.
     * Antes se leía 'id' también en la API, de modo que la regla unique no
     * excluía la propia cita.
     */
    private function citaId(): mixed
    {
        return $this->route('cita') ?? $this->route('id');
    }
}
