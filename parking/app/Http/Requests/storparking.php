<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class storparking extends FormRequest
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
     * @return array<string,
     */
    public function rules(): array
    {
        return [
            'titre' => 'required|string|max:255',
            'adress' => 'required|string|max:255',
            'nombre_total_places' => 'required|integer|min:1',
            'places_disponibles' => ['required','integer','min:0','max:' . $this->input('nombre_total_places', 1)  ],
        ];
    }
}
