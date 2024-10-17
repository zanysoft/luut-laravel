<?php

namespace App\Http\Requests\Api;

use App\Enums\UserCategory;
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


        if ($this->exists('password')){
            $array['password'] = ['required', 'min:6'];
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
            'dob' => ['nullable', 'date_format:Y-m-d'],
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
