<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFacturaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cliente_id'    => 'required|integer|exists:clientes,cliente_id',
            'empleado_id'   => 'required|integer|exists:empleados,empleado_id',
            'fecha_emision' => 'required|date',
            'subtotal'      => 'required|numeric|min:0',
            'total'         => 'required|numeric|min:0',
            'estado'        => 'required|string|in:pendiente,pagada,anulada',
        ];
    }

    public function messages(): array
    {
        return [
            'cliente_id.required'  => 'El cliente es obligatorio.',
            'cliente_id.exists'    => 'El cliente indicado no existe.',
            'empleado_id.required' => 'El empleado es obligatorio.',
            'empleado_id.exists'   => 'El empleado indicado no existe.',
            'total.min'            => 'El total no puede ser negativo.',
            'estado.in'            => 'El estado debe ser pendiente, pagada o anulada.',
        ];
    }
}