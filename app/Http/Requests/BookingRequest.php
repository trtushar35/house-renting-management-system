<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookingRequest extends FormRequest
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
                    'flat_id' => 'required',
					'tenant_id' => 'required',
					'rent' => 'required|numeric',
					'booking_status' => 'required',

                ];
                break;

            case 'PATCH':
            case 'PUT':
                return [
                    'flat_id' => 'required',
					'tenant_id' => 'required',
					'rent' => 'required|numeric',
					'booking_status' => 'required',

                ];
                break;
        }
    }

    public function messages()
    {
        return [
            'flat_id.required' => 'The Flat Number field is required.',
 			'tenant_id.required' => 'The Tenant Name field is required.',
 			'rent.required' => 'The Rent field is required.',
 			'start_date.required' => 'The Start Date field is required.',
 			'booking_status.required' => 'The Booking Status field is required.',



        ];
    }
}
