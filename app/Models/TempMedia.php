<?php

namespace App\Models;

use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class TempMedia extends Model
{
    use MassPrunable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'url',
        'collection_name',
        'disk',
        'is_processed',
        'meta',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_processed' => 'boolean',
            'meta' => 'array',
        ];
    }

    public function prunable()
    {
        return static::query()
            ->where('is_processed', true)
            ->where('updated_at', '<=', now()->subDays(3));
    }

    /**
     * Get the parent mediable model.
     */
    public function mediable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope a query to only include unprocessed media.
     */
    public function scopeUnprocessed($query)
    {
        return $query->where('is_processed', false);
    }

    /**
     * Mark the media as processed.
     */
    public function markAsProcessed(): bool
    {
        return $this->update(['is_processed' => true]);
    }
}
