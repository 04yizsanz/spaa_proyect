<?php

namespace App\Http\Requests\Pago;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Registrar un pago con estado distinto de 'pendiente' exige además el
 * permiso 'pagos.aprobar' (PagoService / PagoPolicy).
 */
class StorePagoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'codigo_cita'    => 'required|integer|exists:citas,codigo_cita',
            'monto'          => 'required|numeric|gt:0',
            'metodo'         => 'required|string|in:efectivo,tarjeta,transferencia,pse',
            'fecha_hora'     => 'required|date',
            'estado'         => 'sometimes|string|in:pendiente,aprobado,rechazado',
        ];
    }

    public function messages(): array
    {
        return [
            'codigo_cita.required'    => 'La cita asociada es obligatoria.',
            'codigo_cita.exists'      => 'La cita indicada no existe.',
            'monto.gt'                => 'El monto debe ser mayor que cero.',
            'metodo.in'               => 'El método de pago no es válido.',
        ];
    }
}
