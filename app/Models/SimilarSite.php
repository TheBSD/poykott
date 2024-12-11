<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SimilarSite extends Model
{
    use HasFactory;

    protected $fillable = [
        'similar_site_category_id',
        'name',
        'url',
        'description',
    ];

    protected $casts = [
        'id' => 'integer',
    ];

    /**
     * Relations
     */
    public function similarSiteCategory(): BelongsTo
    {
        return $this->belongsTo(SimilarSiteCategory::class);
    }
}
