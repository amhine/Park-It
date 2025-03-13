<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class updateparking extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $totalPlaces = $this->input('nombre_total_places', 0); 
        return [
            'titre' => 'sometimes|string',
            'adress' => 'sometimes|string|max:255',
            'nombre_total_places' => 'sometimes|integer|min:1',
            'places_disponibles' => "sometimes|integer|min:0|max:$totalPlaces",
        ];
    }
}
