<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFacturaServicioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cantidad'        => 'sometimes|integer|min:1',
            'precio_unitario' => 'sometimes|numeric|min:0',
        ];
    }
}