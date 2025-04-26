<?php

namespace App\Actions;

class ConvertFormattedNumberToInteger
{
    /**
     * Convert formatted number to integer.
     * For example: $100M -> 100000000
     */
    public function execute(string $formattedValue): ?int
    {
        $cleanValue = str_replace(['$', ','], '', $formattedValue);

        preg_match('/([\d.]+)([MKBT]?)/', $cleanValue, $matches);

        // if the value is text and not suitable numbers
        if (blank($matches)) {
            return null;
        }

        $number = (float) ($matches[1] ?? 0);
        $suffix = strtoupper($matches[2] ?? '');

        $multiplier = match ($suffix) {
            'M' => 1000000,
            'K' => 1000,
            'B' => 1000000000,
            'T' => 1000000000000,
            default => 1,
        };

        return (int) round($number * $multiplier);
    }
}
