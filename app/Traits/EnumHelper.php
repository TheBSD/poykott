<?php

namespace App\Traits;

/**
 * Trait for comfortable working with ENUMs
 */
trait EnumHelper
{
    /**
     * Find ENUM by name or value
     *
     *
     * @return EnumHelper|null
     */
    public static function find(mixed $needle): ?self
    {
        if (in_array($needle, self::names())) {
            return constant("self::$needle");
        }
        if (in_array($needle, self::values())) {
            return self::tryFrom($needle);
        }

        return null;
    }

    /**
     * Get all ENUM names
     */
    public static function names(): array
    {
        return array_column(self::cases(), 'name');
    }

    /**
     * Get all ENUM values
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get all ENUM name => value
     */
    public static function array(): array
    {
        return array_combine(self::values(), self::names());
    }
}
