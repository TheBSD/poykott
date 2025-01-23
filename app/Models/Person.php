<?php

namespace App\Models;

use App\Traits\HasTags;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\URL;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Person extends Model implements HasMedia
{
    use HasFactory;
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
     * Retrieves the appropriate image path:
     * - First checks for an optimized version.
     * - If the optimized version is not available, it checks for the original image.
     * - If neither the optimized nor original image is available, a default image path is returned.
     */
    protected function imagePath(): Attribute
    {
        return Attribute::make(
            get: function () {
                $firstMedia = $this->getMedia()->first();

                $optimizedPath = $firstMedia?->getPath('optimized');
                $optimizedUrl = $firstMedia?->getUrl('optimized');

                $originalPath = $firstMedia?->getPath();
                $originalUrl = $firstMedia?->getUrl();

                $defaultUrl = URL::asset('storage/images/people/default/user.webp');

                if (file_exists($optimizedPath)) {
                    return $optimizedUrl;
                }

                if (file_exists($originalPath)) {
                    return $originalUrl;
                }

                return $defaultUrl;
            }
        );
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
