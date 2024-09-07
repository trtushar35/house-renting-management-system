<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FlatRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        switch ($this->method()) {
            case 'POST':
                return [
                    'house_id' => 'required',
					'address' => 'required|string',
					'floor_number' => 'required|string',
					'flat_number' => 'required|string',
					'num_bedrooms' => 'required|numeric',
					'num_bathrooms' => 'required|numeric',
					'rent' => 'required|numeric',
					'availability' => 'required|boolean',
					'available_date' => 'required'
                ];
                break;

            case 'PATCH':
            case 'PUT':
                return [
                    'house_id' => 'required',
					'address' => 'required|string',
					'floor_number' => 'required|string',
					'flat_number' => 'required|string',
					'num_bedrooms' => 'required|numeric',
					'num_bathrooms' => 'required|numeric',
					'rent' => 'required|numeric',
					'availability' => 'required|boolean',
					'available_date' => 'required'
                ];
                break;
        }
    }

    public function messages()
    {
        return [
            'house_id.required' => 'The House Address field is required.',
 			'address.required' => 'The House Address field is required.',
 			'floor_number.required' => 'The Floor Number field is required.',
 			'num_bedrooms.required' => 'The Num Bedrooms field is required.',
 			'num_bathrooms.required' => 'The Num Bathrooms field is required.',
 			'rent.required' => 'The Rent field is required.',
 			'availability.required' => 'The Availability field is required.',
 			'available_date.required' => 'The Available Date field is required.',
        ];
    }
}
