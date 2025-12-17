<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'duration_minutes' => 'required|integer|in:30,60',
            'price' => 'required|numeric|min:0|max:999999.99',
            'active' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'El nombre del servicio es obligatorio',
            'duration_minutes.required' => 'La duración es obligatoria',
            'duration_minutes.in' => 'La duración debe ser 30 o 60 minutos',
            'price.required' => 'El precio es obligatorio',
            'price.numeric' => 'El precio debe ser un número',
            'price.min' => 'El precio no puede ser negativo',
        ];
    }
}
