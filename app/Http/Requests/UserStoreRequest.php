<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UserStoreRequest extends FormRequest
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
        $array = [];
        if (request()->segment(1) != 'api') {
            $array = [
                'email' => ['required', 'max:50', 'email',
                ],
                'phone' => ['nullable', 'regex:/^([0-9\s\-\+\(\)]*)$/'],
                'roles' => ['required'],
                'dob' => ['nullable', 'before:'.date('Y-m-d')],
                'created_by' => ['nullable'],
                'user_category' => ['nullable'],
                'address' => ['nullable'],
                'gender' => ['nullable'],
                'country_id' => ['nullable'],
                'password' => 'required|string|min:8',
            ];
        }
        if (request()->segment(3) != 'guest' && request()->segment(1) == 'api') {
            $array = [
                //'password' => ['required', 'confirmed', Password::min(8)->letters()->mixedCase()->numbers()->symbols()],
                'password' => 'required|string|min:8|confirmed|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/',
                'email' => ['required', 'max:50', 'regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix',
                ],
            ];
        }

        return array_merge([
            'first_name' => ['nullable', 'max:190', 'regex:/^[\pL\s\-]+$/u'],
            'last_name' => ['nullable', 'max:190', 'regex:/^[\pL\s\-]+$/u'],
            'email' => ['required', 'max:50', 'email', 'regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix'],
            'user_category' => ['nullable'],
            'ip_address' => ['nullable'],
            'geo_location' => ['nullable'],
        ], $array);
    }

    public function messages()
    {
        return [
            'password.regex' => 'Your password should contain at-least 1 Uppercase, 1 Lowercase, 1 Numeric and 1 special character',

        ];
    }
}
