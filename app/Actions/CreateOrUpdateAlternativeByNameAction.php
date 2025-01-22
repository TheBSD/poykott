<?php

namespace App\Actions;

use App\Models\Alternative;
use Illuminate\Support\Str;

class CreateOrUpdateAlternativeByNameAction
{
    /**
     * Creates a new alternative or updates an existing one based on name.
     *
     * This action handles two types of fields differently:
     * - Optional Fields: Only updated when the existing value is empty
     * - Forced Fields: Always updated regardless of existing values
     *
     * Note: We use a custom implementation instead of Laravel's `updateOrCreate`
     * because SQLite doesn't support case-insensitive comparison in `whereRaw`.
     *
     * @param  string  $alternativeName  The name to search for or create with
     * @param  array  $forcedFields  Fields that will always be updated
     * @param  array  $optionalFields  Fields that will only be updated if empty
     */
    public function execute(
        string $alternativeName,
        array $forcedFields = [],
        array $optionalFields = [],
    ): Alternative {
        $alternative = $this->findAlternativeByName($alternativeName);

        if (is_null($alternative)) {
            return $this->createAlternative($alternativeName, $optionalFields, $forcedFields);
        }

        return $this->updateAlternative($alternative, $optionalFields, $forcedFields);
    }

    private function findAlternativeByName(string $alternativeName)
    {
        $normalizedName = Str::of($alternativeName)->lower()->trim()->value();

        return Alternative::query()
            ->whereRaw('LOWER(name) = ?', [$normalizedName])
            ->first();
    }

    private function createAlternative(string $name, array $optionalFields, array $forcedFields): Alternative
    {
        return Alternative::query()->create(array_merge(
            ['name' => trim($name)],
            $optionalFields,
            $forcedFields
        ));
    }

    private function updateAlternative(Alternative $alternative, array $optionalFields, array $forcedFields): Alternative
    {
        $fieldsToUpdate = array_merge(
            $this->getEmptyFieldsToUpdate($alternative, $optionalFields),
            $this->getForcedFieldsToUpdate($forcedFields)
        );

        return tap($alternative)->update($fieldsToUpdate);
    }

    private function getEmptyFieldsToUpdate(Alternative $alternative, array $optionalFields): array
    {
        return array_filter(
            $optionalFields,
            fn ($key) => blank($alternative->{$key}),
            ARRAY_FILTER_USE_KEY
        );
    }

    private function getForcedFieldsToUpdate(array $forcedFields): array
    {
        return $forcedFields;
    }
}
