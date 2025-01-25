<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class NewCompanyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, (ValidationRule | array<mixed> | string)>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:companies,name', 'trim'],
            'email' => ['required', 'email:rfc,dns'],
            'p_email' => ['email:rfc,dns', 'nullable'],
            'url' => ['required', 'url', 'active_url'],
            'icon_url' => ['url', 'nullable', 'active_url'],
            'short_description' => ['string', 'nullable', 'max:255', 'trim'],
            'description' => ['string', 'nullable', 'max:10000', 'trim'],
            'tags' => ['string', 'nullable'],
            'office_locations' => ['string', 'nullable', 'max:1000'],
            'resources' => ['url', 'nullable', 'max:1000'],
        ];
    }
}
