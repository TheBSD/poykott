<?php

namespace App\Models;

use App\Traits\HasImagePath;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class SimilarSite extends Model implements Auditable, HasMedia
{
    use HasFactory;
    use HasImagePath;
    use InteractsWithMedia;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'similar_site_category_id',
        'name',
        'url',
        'description',
    ];

    /**
     * =====================
     *  Packages configurations
     * =====================
     */
    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('optimized')->optimize()->format('webp');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('default')->singleFile();
    }

    /**
     * =====================
     *  Relations
     * =====================
     */
    public function similarSiteCategory(): BelongsTo
    {
        return $this->belongsTo(SimilarSiteCategory::class);
    }
}
