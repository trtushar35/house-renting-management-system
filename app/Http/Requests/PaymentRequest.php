<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentRequest extends FormRequest
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
                    'booking_id' => 'required|numeric',
					'payment_date' => 'required|date',
					'payment_month' => 'required|date',
					'amount' => 'required|numeric',
					'paid_amount' => 'required|numeric',
					'due' => 'required|numeric',
					'payment_method' => 'required|string'
                ];
                break;

            case 'PATCH':
            case 'PUT':
                return [
                    'booking_id' => 'required|numeric',
					'payment_date' => 'required|date',
					'payment_month' => 'required|date',
					'amount' => 'required|numeric',
					'paid_amount' => 'required|numeric',
					'due' => 'required|numeric',
					'payment_method' => 'required|string'
                ];
                break;
        }
    }

    public function messages()
    {
        return [
            'booking_id.required' => 'The Booking Tenant Name field is required.',
 			'payment_date.required' => 'The Payment Date field is required.',
 			'payment_month.required' => 'The Payment Month field is required.',
 			'amount.required' => 'The Amount field is required.',
 			'paid_amount.required' => 'The Pay Amount field is required.',
 			'due.required' => 'The Due field is required.',
 			'payment_method.required' => 'The Payment Method field is required.',


        ];
    }
}
