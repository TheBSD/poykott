<?php

namespace App\Actions;

use App\Models\Tag;
use Illuminate\Support\Str;

class FindOrCreateTagByNameAction
{
    /**
     * Creates a new tag or updates an existing one based on name.
     *
     * Note: We use a custom implementation instead of Laravel's `firstOrCreate`
     * because SQLite doesn't support case-insensitive comparison in `whereRaw`.
     *
     * @param  string  $tagName  The name to search for or create with
     */
    public function execute(string $tagName): Tag
    {
        $lowerTagName = Str::of($tagName)->lower()->trim()->value();
        $tag = Tag::query()->whereRaw('LOWER(name) = ?', [$lowerTagName])->first();

        if (is_null($tag)) {
            return Tag::query()->create([
                'name' => Str::of($tagName)->squish()->value(),
            ]);
        }

        return $tag;
    }
}
