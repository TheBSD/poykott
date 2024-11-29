<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SimilarSite extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'url',
        'description',
        'parent_id',
    ];

    protected $casts = [
        'id' => 'integer',
        'parent_id' => 'integer',
    ];

    /**
     * Relations
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(SimilarSite::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(SimilarSite::class, 'parent_id');
    }
}
