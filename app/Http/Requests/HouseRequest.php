<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HouseRequest extends FormRequest
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
                    'house_owner_id' => 'required',
                    'house_name' => 'required',
                    'house_number' => 'required',
					'address' => 'required|string',
					'division' => 'required|string',
					'city' => 'required|string',
					'country' => 'required|string'
                ];
                break;

            case 'PATCH':
            case 'PUT':
                return [
                    'house_owner_id' => 'required',
                    'house_name' => 'required',
                    'house_number' => 'required',
					'address' => 'required|string',
					'division' => 'required|string',
					'city' => 'required|string',
					'country' => 'required|string'
                ];
                break;
        }
    }

    public function messages()
    {
        return [
            'house_owner_id.required' => 'The House Owner Name field is required.',
            'house_name.required' => 'The House Name field is required.',
            'house_number.required' => 'The House Number field is required.',
 			'address.required' => 'The Address field is required.',
 			'division.required' => 'The Division field is required.',
 			'city.required' => 'The City field is required.',
 			'country.required' => 'The Country field is required.',


        ];
    }
}
