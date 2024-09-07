<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FlatImageRequest extends FormRequest
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
					'square_footage.*' => 'required|file'
                ];
                break;

            case 'PATCH':
            case 'PUT':
                return [
                    'flat_id' => 'required',
					'square_footage.*' => 'nullable'
                ];
                break;
        }
    }

    public function messages()
    {
        return [
            'flat_id.required' => 'The Flat Number field is required.',
 			'square_footage.required' => 'The Square Footage field is required.',


        ];
    }
}
