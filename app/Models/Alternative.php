<?php

namespace App\Models;

use App\Traits\HasTags;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Facades\URL;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * @property int $id
 * @property string $name
 * @property string $description
 * @property Carbon $approved_at
 * @property string $logo
 * @property string $notes
 * @property string $url
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Alternative extends Model
{
    use HasFactory;
    use HasTags;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'description', 'approved_at', 'logo', 'notes', 'url'];

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'approved_at' => 'timestamp',
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

                $defaultUrl = URL::asset('storage/images/people/default/company.webp');

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
    public function registerMediaConversions(?Media $media = null): void
    {
        $this
            ->addMediaConversion('optimized')
            ->optimize()
            ->format('webp');
    }

    /**
     * Scopes
     */
    public function scopeApproved($query)
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

    public function logo(): MorphOne
    {
        return $this->morphOne(Image::class, 'imageable');
    }
}
