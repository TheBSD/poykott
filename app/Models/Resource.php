<?php

namespace App\Models;

use App\Enums\ResourceType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property int $id
 * @property string $title
 * @property string $type
 * @property string $description
 * @property string $url
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Resource extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'type',
        'description',
        'url',
        'resourceable_id',
        'resourceable_type',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'type' => ResourceType::class,
        ];
    }

    /**
     * Relations
     */
    public function resourceable(): MorphTo
    {
        return $this->morphTo();
    }
}
