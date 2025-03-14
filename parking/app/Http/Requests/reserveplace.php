<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class reserveplace extends FormRequest
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
        return [
            
                'heure_arrivee' => 'required|date_format:Y-m-d H:i',
                'heure_depart' => 'required|date_format:Y-m-d H:i|after:heure_arrivee',
                'parking_id' => 'required|exists:parkings,id',
            
        ];
    }
}
