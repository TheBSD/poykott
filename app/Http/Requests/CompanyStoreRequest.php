<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompanyStoreRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'category_id' => ['required', 'integer', 'exists:Categories,id'],
            'exit_strategy_id' => ['nullable', 'integer', 'exists:exit_strategies,id'],
            'funding_level_id' => ['nullable', 'integer', 'exists:funding_levels,id'],
            'company_size_id' => ['nullable', 'integer', 'exists:company_sizes,id'],
            'approved_at' => ['nullable'],
            'name' => ['required', 'string'],
            'slug' => ['required', 'string', 'unique:companies,slug'],
            'description' => ['nullable', 'string'],
            'logo' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'valuation' => ['nullable', 'integer'],
            'exit_valuation' => ['nullable', 'integer'],
            'stock_symbol' => ['nullable', 'string'],
            'url' => ['required', 'string'],
            'total_funding' => ['nullable', 'integer'],
            'last_funding_date' => ['nullable', 'date'],
            'headquarter' => ['nullable', 'string'],
            'founded_at' => ['nullable', 'date'],
            'office_locations' => ['nullable', 'json'],
            'employee_count' => ['nullable', 'integer'],
        ];
    }
}
