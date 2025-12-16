<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGalleryItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'title' => 'nullable|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120', // 5MB max
            'active' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'image.required' => 'La imagen es obligatoria',
            'image.image' => 'El archivo debe ser una imagen',
            'image.mimes' => 'La imagen debe ser jpeg, png, jpg o webp',
            'image.max' => 'La imagen no puede superar los 5MB',
        ];
    }
}
