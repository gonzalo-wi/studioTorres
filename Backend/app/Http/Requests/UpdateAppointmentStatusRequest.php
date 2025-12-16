<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAppointmentStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null; // Must be authenticated
    }

    public function rules(): array
    {
        return [
            'status' => 'required|in:PENDING,CONFIRMED,CANCELLED,DONE',
            'date' => 'nullable|date|after_or_equal:today',
            'time' => 'nullable|date_format:H:i',
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'El estado es obligatorio',
            'status.in' => 'El estado debe ser: PENDING, CONFIRMED, CANCELLED o DONE',
            'date.after_or_equal' => 'No puedes reprogramar para fechas pasadas',
            'time.date_format' => 'El formato del horario es inválido',
        ];
    }
}
