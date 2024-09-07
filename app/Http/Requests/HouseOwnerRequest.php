<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HouseOwnerRequest extends FormRequest
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
                    'name' => 'required|string',
					'email' => 'required|string',
					'phone' => 'required|string',
					'address' => 'required|string',
					'password' => 'required|string',
                ];
                break;

            case 'PATCH':
            case 'PUT':
                return [
                    'name' => 'required|string',
					'email' => 'required|string',
					'phone' => 'required|string',
					'address' => 'required|string'
                ];
                break;
        }
    }

    public function messages()
    {
        return [
            'name.required' => 'The Name field is required.',
 			'email.required' => 'The Email field is required.',
 			'phone.required' => 'The Phone field is required.',
 			'address.required' => 'The Address field is required.',
 			'password.required' => 'The Password field is required.',


        ];
    }
}
