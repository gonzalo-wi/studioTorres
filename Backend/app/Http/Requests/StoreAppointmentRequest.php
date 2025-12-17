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
            'barber_id' => 'required|exists:barbers,id',
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required|date_format:H:i',
            'client_name' => 'required|string|min:2|max:255',
            'client_phone' => ['required', 'string', 'regex:/^(\+?54)?[0-9]{10,13}$/'],
            'client_email' => 'nullable|email|max:255',
            'notes' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'service_id.required' => 'Debes seleccionar un servicio',
            'service_id.exists' => 'El servicio seleccionado no existe',
            'barber_id.required' => 'Debes seleccionar un barbero',
            'barber_id.exists' => 'El barbero seleccionado no existe',
            'date.required' => 'La fecha es obligatoria',
            'date.after_or_equal' => 'No puedes reservar para fechas pasadas',
            'time.required' => 'El horario es obligatorio',
            'time.date_format' => 'El formato del horario es inválido',
            'client_name.required' => 'El nombre es obligatorio',
            'client_name.min' => 'El nombre debe tener al menos 2 caracteres',
            'client_phone.required' => 'El teléfono es obligatorio',
            'client_phone.regex' => 'El formato del teléfono es inválido',
            'client_email.email' => 'El formato del email es inválido',
            'notes.max' => 'Las observaciones no pueden superar los 500 caracteres',
        ];
    }
}
