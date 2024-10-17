<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRegisterRequest extends FormRequest
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
            'first_name' => ['nullable', 'max:190'],
            'last_name' => ['nullable', 'max:190'],
            'email' => ['required','email', 'max:50', Rule::unique('users')],
            'password' => ['required', 'min:6'],
        ];
    }
}
