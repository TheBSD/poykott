<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use OwenIt\Auditing\Contracts\Auditable;

class SocialLink extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'linkable_type',
        'linkable_id',
        'url',
    ];

    /**
     * @see \App\Models\Company
     * @see \App\Models\Person
     */
    public function linkable(): MorphTo
    {
        return $this->morphTo();
    }

    public function platform(): Attribute
    {
        return Attribute::get(function (): string {
            $host = parse_url($this->url, PHP_URL_HOST);

            $platformDomains = [
                'facebook' => ['facebook.com', 'fb.com'],
                'twitter' => ['twitter.com', 'x.com'],
                'linkedin' => ['linkedin.com'],
                'instagram' => ['instagram.com', 'instagr.am'],
                'youtube' => ['youtube.com', 'youtu.be'],
            ];

            foreach ($platformDomains as $platform => $domains) {
                foreach ($domains as $domain) {
                    if (str_contains($host, $domain)) {
                        return $platform;
                    }
                }
            }

            return 'unknown';
        });
    }
}
