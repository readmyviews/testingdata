<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'first_name' => 'required|max:30',
            'last_name' => 'required|max:30',
            'gender' => 'required',
            'email' => [
                'email',
                'required',
                Rule::unique('users')->ignore($this->user()),
            ],
            'file' => [
                'image',
                'mimes:jpeg,png,jpg,gif,svg|max:2048'
            ],
        ];
    }
}
