<?php

namespace App\Models;

use App\Traits\HasTags;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

/**
 * @property int $id
 * @property string $name
 * @property string $avatar
 * @property string $job_title
 * @property Carbon $approved_at
 * @property string $location
 * @property string $biography
 * @property string $social_links
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
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
        'avatar',
        'job_title',
        'approved_at',
        'location',
        'biography',
        'social_links',
        'url',
        'description',
    ];

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
        $this->addMediaConversion('optimized')->optimize()->format('webp');
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

    /**
     * Scopes
     */
    public function scopeNonEmptyAvatar(Builder $query): Builder
    {
        return $query->whereRaw("avatar IS NOT NULL and avatar != ''");
    }

    /**
     * The attributes that should be cast to native types.
     */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'approved_at' => 'timestamp',
            'social_links' => 'array',
        ];
    }
}
