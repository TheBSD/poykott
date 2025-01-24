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
            'name' => ['required', 'string'],
            'email' => ['required', 'email'],
            'p_email' => ['email', 'nullable'],
            'url' => ['required', 'url'],
            'icon_url' => ['url', 'nullable'],
            'short_description' => ['string', 'nullable'],
            'description' => ['string', 'nullable'],
            'tags' => ['string', 'nullable'],
            'office_locations' => ['string', 'nullable'],
            'resources' => ['url', 'nullable'],
        ];
    }
}
