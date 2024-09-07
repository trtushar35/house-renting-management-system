<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RoomRequest extends FormRequest
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
					'room_number' => 'required|string',
					'type' => 'required|in:single,double',
					'rent' => 'required|string',
					'availability' => 'required'
                ];
                break;

            case 'PATCH':
            case 'PUT':
                return [
                    'house_id' => 'required',
					'room_number' => 'required|string',
					'type' => 'required|in:single,double',
					'rent' => 'required|string',
					'availability' => 'required'
                ];
                break;
        }
    }

    public function messages()
    {
        return [
            'house_id.required' => 'The House Address field is required.',
 			'room_number.required' => 'The Room Number field is required.',
 			'type.required' => 'The Type field is required.',
 			'type.in' => 'The selected value for type is invalid.',
 			'rent.required' => 'The Rent field is required.',
 			'availability.required' => 'The Availability field is required.',


        ];
    }
}
