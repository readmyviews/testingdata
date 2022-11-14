<?php

namespace App\Http\Requests\RoleManagement;

use App\Traits\CommonTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    use CommonTrait;

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
        $rules = [
            'first_name' => 'required|max:30',
            'last_name' => 'required|max:30',
            'middle_name' => 'required|max:30',
            'gender' => 'required',
            'status' => 'required',
            'user_role' => 'required',
            'email' => [
                'email',
                'required',
                'unique:users,email',
                'max:30',
            ],
            'file' => [
                'image',
                'mimes:jpeg,png,jpg,gif,svg|max:2048',
            ],
            'phone_no' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10',
            'country_id' => 'required',
            'state_id' => 'required_unless:country_id,' . $this->getTrinidadTobagoId(),
            'city_id' => 'required',
            'address' => 'required|string|max:450',
            'postal_code' => 'required_unless:country_id,' . $this->getTrinidadTobagoId() . '|nullable|numeric|integer|digits:9',
        ];

        if ($this->getMethod() == 'PUT') {
            $uuid = $this->route('user');
            $rules['email'] = [
                'email',
                'required',
                // Rule::unique('users')->ignore($this->user->id),
                Rule::unique('users')->ignore($uuid, 'uuid'),
            ];
        }

        return $rules;
    }
}