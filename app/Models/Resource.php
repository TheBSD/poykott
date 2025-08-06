<?php

namespace App\Models;

use App\Enums\ResourceType;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use OwenIt\Auditing\Contracts\Auditable;

class Resource extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type',
        'notes',
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
     * Generate an archive.org reference URL using the resource's URL and creation timestamp.
     *
     *
     * @todo test this method to be sure
     */
    public function archiveUrl(): Attribute
    {
        return Attribute::get(function (): string {
            $encodedUrl = urlencode($this->url);
            $date = $this->created_at->format('YmdHis');

            return "https://web.archive.org/web/{$date}/{$encodedUrl}";
        });
    }

    /**
     * Relations
     */
    public function resourceable(): MorphTo
    {
        return $this->morphTo();
    }
}
