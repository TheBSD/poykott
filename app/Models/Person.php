<?php

namespace App\Models;

use App\Traits\HasImagePath;
use App\Traits\HasTags;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Person extends Model implements HasMedia
{
    use HasFactory;
    use HasImagePath;
    use HasSlug;
    use HasTags;
    use InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
        'job_title',
        'approved_at',
        'location',
        'notes',
        'social_links',
        'url',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'approved_at' => 'timestamp',
            'social_links' => 'array',
            'notes' => 'collection',
        ];
    }

    /**
     * Overrides the imagePath method from the HasImagePath trait
     */
    protected function imagePath(): Attribute
    {
        return $this->resolveImagePath('default', 'storage/images/people/default/user.webp');
    }

    /**
     * Packages configurations
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this
            ->addMediaConversion('optimized')
            ->optimize()
            ->format('webp');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('default')->singleFile();
    }

    /**
     * Scopes
     */
    public function scopeApproved(Builder $query): Builder
    {
        return $query->whereNotNull('approved_at');
    }

    /**
     * Relations
     */
    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(Company::class);
    }

    public function resources(): MorphMany
    {
        return $this->morphMany(Resource::class, 'resourceable');
    }
}
