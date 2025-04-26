<?php

namespace App\Actions;

class CheckAccentedCharacterAction
{
    /**
     * Check if the string contains accented characters like Á or È or any character than English
     */
    public function execute(string $name): bool
    {
        $ascii = transliterator_transliterate('Any-Latin; Latin-ASCII; [:Nonspacing Mark:] Remove; NFC;', $name);

        return $ascii !== $name;
    }
}
