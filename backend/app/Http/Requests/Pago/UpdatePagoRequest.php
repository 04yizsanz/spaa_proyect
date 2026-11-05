<?php

namespace App\Http\Requests\Pago;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Solo los pagos 'pendiente' son editables (PagoPolicy) y cambiar el estado
 * exige 'pagos.aprobar' (PagoService).
 */
class UpdatePagoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'codigo_cita'    => 'sometimes|integer|exists:citas,codigo_cita',
            'monto'          => 'sometimes|numeric|gt:0',
            'metodo'         => 'sometimes|string|in:efectivo,tarjeta,transferencia,pse',
            'fecha_hora'     => 'sometimes|date',
            'estado'         => 'sometimes|string|in:pendiente,aprobado,rechazado',
        ];
    }

    public function messages(): array
    {
        return [
            'codigo_cita.exists' => 'La cita indicada no existe.',
            'monto.gt'           => 'El monto debe ser mayor que cero.',
            'metodo.in'          => 'El método de pago no es válido.',
        ];
    }
}
