<?php

namespace App\Http\Requests\RoleManagement;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class UpdateRoleRequest extends FormRequest
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
    public function rules(Request $request)
    {
        return [
            'name' => 'required|regex:/^[a-zA-Z ._]+$/u|unique:roles,name,' . $request->uuid.',uuid',
            'status' => 'required',
            'permissions' => 'nullable',
        ];
    }
    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.regex' => 'The name should have only characters.',
        ];
    }
}