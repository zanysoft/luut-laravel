<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::check();
    }

    public function rules()
    {
        $array = [];

        if ($this->exists('password')) {
            $array['password'] = ['required', 'min:6'];
        }

        if (request()->segment(1) == 'admin') {
            $array = [
                'email' => [
                    'required', 'max:50', 'email',
                    Rule::unique('users')->ignore($this->route('user')->id),
                ],
                'username' => [
                    'nullable', 'max:50', 'regex:/^([a-z])+?([a-z0-9])+$/i',
                    Rule::unique('users', 'username')->ignore($this->route('user')->id),
                ],
            ];
        }

        if ($this->segment(1) == 'api') {
            $array['username'] = [
                'nullable', 'max:50', 'regex:/^([a-z])+?([a-z0-9])+$/i',
                Rule::unique('users', 'username')->ignore($this->user()->id),
            ];
        }

        return array_merge([
            'first_name' => ['nullable', 'max:190'],
            'last_name' => ['nullable', 'max:190'],
            'password' => ['nullable', 'min:8'],
            'dob' => ['nullable', 'before: 18 years ago', 'date_format:Y-m-d'],
            'age' => ['nullable'],
            'gender' => ['nullable'],
            'about' => ['nullable', 'max:600'],

            'phone' => ['nullable'],
            'address' => ['nullable'],
            'address2' => ['nullable'],
            'city' => ['nullable'],
            'country' => ['nullable'],

            'latitude' => ['nullable'],
            'longitude' => ['nullable'],
        ], $array);
    }
}
