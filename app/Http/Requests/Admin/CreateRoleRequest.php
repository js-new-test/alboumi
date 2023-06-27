<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CreateRoleRequest extends FormRequest
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
          'role_title' => 'required',
          'role_type' => 'required',  
        ];
    }

    public function messages()
    {
        return [
            'role_title.required' => 'Role Tile is required (Max length is 255 character)',            
        ];
    }
}
