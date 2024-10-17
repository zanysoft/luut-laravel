<?php


namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class PackagesRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = $this->get('id');

        $rules = [
            'title' => ['required', "min:10"],
            //'slug' => ['nullable', Rule::unique('packages', 'slug')],
        ];

        if ($this->get('package_type') == 'subscription') {
            if ($this->has('stripe_plan')) {
                $rules['stripe_plan'] = ['required'];
            }
            if ($this->has('payment_plan')) {
                $rules['payment_plan'] = ['required'];
            }
        } else {
            $rules['amount'] = ['required'];
        }

        if ($id) {
            //$rules['slug'] = ['nullable', Rule::unique('packages', 'slug')->ignore($id)];
        }

        if ($this->exists('is_custom_package') && $this->get('is_custom_package') == 1) {
            if (!$this->filled('custom_package_name')) {
                $rules['payment_plan'] = ['required'];
            }
            $rules['amount'] = ['required'];
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'title.required' => 'Package title is required.',
            'description.required' => 'Package description is required.',
            'stripe_plan.required' => 'Stripe product is required.',
            'payment_plan.required' => 'Payment plan is required.',
            'picture.required' => 'Package picture is required.'
        ];
    }
}
