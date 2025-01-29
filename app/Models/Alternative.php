<?php

namespace App\Models;

use App\Traits\HasImagePath;
use App\Traits\HasTags;
use App\Traits\Media\HasFileMigration;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Alternative extends Model implements HasMedia
{
    use HasFactory;
    use HasFileMigration;
    use HasImagePath;
    use HasTags;
    use InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'description', 'approved_at', 'notes', 'url'];

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'approved_at' => 'timestamp',
        ];
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
}
