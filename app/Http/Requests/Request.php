<?php
namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class Request extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // Only allow creates if the user is logged in
        return auth()->check();
    }


    public function getValidatorInstance()
    {
        if ($this->request->has('slug')) {
            $this->merge([
                'slug' => Str::slug($this->request->get('slug'))
            ]);
        }

        return parent::getValidatorInstance();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            // 'name' => ['required', 'min:3', 'max:255'],
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param Validator $validator
     * @throws ValidationException
     */
    protected function failedValidation(Validator $validator)
    {
        if ($this->ajax() || $this->wantsJson() || $this->segment(1) == 'api') {
            // Get Errors
            $errors = (new ValidationException($validator))->errors();

            // Get Json
            $json = [
                'success' => false,
                'message' => __('An error occurred while validating the data.'),
                'errors' => $errors,
            ];

            throw new HttpResponseException(response()->json($json, JsonResponse::HTTP_UNPROCESSABLE_ENTITY));
        }

        parent::failedValidation($validator);
    }
}
