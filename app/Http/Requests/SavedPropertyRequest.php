<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SavedPropertyRequest extends FormRequest
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
                    'tenant_id' => '',
					'owner_id' => '',
					'flat_id' => ''
                ];
                break;

            case 'PATCH':
            case 'PUT':
                return [
                    'tenant_id' => '',
					'owner_id' => '',
					'flat_id' => ''
                ];
                break;
        }
    }

    public function messages()
    {
        return [
            'tenant_id.required' => 'The tenant_id field is required.',
 			'owner_id.required' => 'The owner_id field is required.',
 			'flat_id.required' => 'The flat_id field is required.',
 			

        ];
    }
}
