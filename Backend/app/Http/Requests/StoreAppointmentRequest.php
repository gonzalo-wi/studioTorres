<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Public endpoint
    }

    public function rules(): array
    {
        return [
            'service_id' => 'required|exists:services,id',
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required|date_format:H:i',
            'customer_name' => 'required|string|min:2|max:255',
            'phone' => ['required', 'string', 'regex:/^(\+?54)?[0-9]{10,13}$/'],
            'notes' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'service_id.required' => 'Debes seleccionar un servicio',
            'service_id.exists' => 'El servicio seleccionado no existe',
            'date.required' => 'La fecha es obligatoria',
            'date.after_or_equal' => 'No puedes reservar para fechas pasadas',
            'time.required' => 'El horario es obligatorio',
            'time.date_format' => 'El formato del horario es inválido',
            'customer_name.required' => 'El nombre es obligatorio',
            'customer_name.min' => 'El nombre debe tener al menos 2 caracteres',
            'phone.required' => 'El teléfono es obligatorio',
            'phone.regex' => 'El formato del teléfono es inválido',
            'notes.max' => 'Las observaciones no pueden superar los 500 caracteres',
        ];
    }
}
