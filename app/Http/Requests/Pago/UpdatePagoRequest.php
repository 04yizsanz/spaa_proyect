<?php

namespace App\Http\Requests\Pago;

use Illuminate\Foundation\Http\FormRequest;

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
            'monto'          => 'sometimes|numeric|min:0',
            'metodo'         => 'sometimes|string|in:efectivo,tarjeta,transferencia,pse',
            'fecha_hora'     => 'sometimes|date',
            'estado'         => 'sometimes|string|in:pendiente,aprobado,rechazado',
        ];
    }
}