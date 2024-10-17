<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Enums\UserCategory;

class ProfileRequest extends FormRequest
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

       // $this->has()

        if (request()->segment(1) != 'api' && request('user_category') != UserCategory::Website_User->value) {
            $array = [
                'email' => [
                    'required', 'max:50', 'email', 'regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix',
                    Rule::unique('users')->ignore($this->route('user')->id),
                ],
                'username' => [
                    'nullable', 'max:50', 'regex:/^([a-z])+?([a-z0-9])+$/i',
                    Rule::unique('users', 'username')->ignore($this->route('user')->id),
                ],
                'roles' => ['required'],
                'user_category' => ['nullable'],
            ];
        }

        if (request()->segment(1) != 'api' && request('user_category') == UserCategory::Website_User->value) {
            $array = [
                'email' => [
                    'required', 'max:50', 'email', 'regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix',
                    Rule::unique('users')->ignore($this->route('user')->id),
                ],
                'username' => [
                    'nullable', 'max:50', 'regex:/^([a-z])+?([a-z0-9])+$/i',
                    Rule::unique('users', 'username')->ignore($this->route('user')->id),
                ],
                'user_category' => ['nullable'],
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

            'phone' => ['nullable', 'regex:/^([0-9\s\-\+\(\)]*)$/'],
            'address' => ['nullable'],
            'city_id' => ['nullable'],
            'country_id' => ['nullable'],
            'created_by' => ['nullable'],
            'ip_address' => ['nullable'],
            'geo_location' => ['nullable'],

            'latitude' => ['nullable'],
            'longitude' => ['nullable'],

            'traveling_date_from' => ['nullable', 'date_format:Y-m-d'],
            'traveling_date_to' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:traveling_date_from'],
            'relationship_status' => ['nullable'],
            'sexual_orientation' => ['nullable'],

            'interests' => ['nullable'],
            'fields_privacy' => ['nullable'],
            'languages' => ['nullable'],
            'pronouns' => ['nullable'],
        ], $array);
    }
}
