<?php

namespace App\Traits;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait HasTags
{
    public function tags(): Collection
    {
        return $this->tagsRelation;
    }

    public function syncTags(\Illuminate\Support\Collection|array $tags): void
    {
        $this->save();
        $this->tagsRelation()->sync($tags);

        $this->unsetRelation('tagsRelation');
    }

    public function removeTags(): void
    {
        $this->tagsRelation()->detach();

        $this->unsetRelation('tagsRelation');
    }

    public function tagsRelation(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable')->withTimestamps();
    }

    public function hasTag(Tag $tag): bool
    {
        return $this->tagsRelation()->where('tag_id', $tag->id)->exists();
    }

    public function doesntHaveTag(Tag $tag): bool
    {
        return ! $this->hasTag($tag);
    }
}
