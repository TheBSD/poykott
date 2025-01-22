<?php

namespace App\Actions;

use App\Models\Company;
use Illuminate\Support\Str;

class CreateOrUpdateCompanyByNameAction
{
    /**
     * Creates a new company or updates an existing one based on name.
     *
     * This action handles two types of fields differently:
     * - Optional Fields: Only updated when the existing value is empty
     * - Forced Fields: Always updated regardless of existing values
     *
     * Note: We use a custom implementation instead of Laravel's `updateOrCreate`
     * because SQLite doesn't support case-insensitive comparison in `whereRaw`.
     *
     * @param  string  $companyName  The name to search for or create with
     * @param  array  $forcedFields  Fields that will always be updated
     * @param  array  $optionalFields  Fields that will only be updated if empty
     */
    public function execute(
        string $companyName,
        array $forcedFields = [],
        array $optionalFields = [],
    ): Company {
        $company = $this->findCompanyByName($companyName);

        if (is_null($company)) {
            return $this->createCompany($companyName, $optionalFields, $forcedFields);
        }

        return $this->updateCompany($company, $optionalFields, $forcedFields);
    }

    private function findCompanyByName(string $companyName): ?Company
    {
        $normalizedName = Str::of($companyName)->lower()->trim()->value();

        return Company::query()
            ->whereRaw('LOWER(name) = ?', [$normalizedName])
            ->first();
    }

    private function createCompany(string $name, array $optionalFields, array $forcedFields): Company
    {
        return Company::query()->create(array_merge(
            ['name' => trim($name)],
            $optionalFields,
            $forcedFields
        ));
    }

    private function updateCompany(Company $company, array $optionalFields, array $forcedFields): Company
    {
        $fieldsToUpdate = array_merge(
            $this->getEmptyFieldsToUpdate($company, $optionalFields),
            $this->getForcedFieldsToUpdate($forcedFields)
        );

        return tap($company)->update($fieldsToUpdate);
    }

    private function getEmptyFieldsToUpdate(Company $company, array $optionalFields): array
    {
        return array_filter(
            $optionalFields,
            fn ($key) => blank($company->{$key}),
            ARRAY_FILTER_USE_KEY
        );
    }

    private function getForcedFieldsToUpdate(array $forcedFields): array
    {
        return $forcedFields;
    }
}
