<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFacturaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cliente_id'    => 'sometimes|integer|exists:clientes,cliente_id',
            'empleado_id'   => 'sometimes|integer|exists:empleados,empleado_id',
            'fecha_emision' => 'sometimes|date',
            'subtotal'      => 'sometimes|numeric|min:0',
            'total'         => 'sometimes|numeric|min:0',
            'estado'        => 'sometimes|string|in:pendiente,pagada,anulada',
        ];
    }
}