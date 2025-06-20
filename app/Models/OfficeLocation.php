<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class OfficeLocation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'old_name',
        'lat',
        'lng',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'lat' => 'decimal',
            'lng' => 'decimal',
        ];
    }

    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(Company::class);
    }

    public function limitedCompanies(int $limit = 10): BelongsToMany
    {
        return $this->belongsToMany(Company::class)->take($limit);
    }

    /**
     * Get the city part from the location.
     */
    public function getCity(): ?string
    {
        $parts = explode(',', $this->name);

        return trim((string) ($parts[0] ?? null));
    }

    /**
     * Get the country part from the location.
     */
    public function getCountry(): ?string
    {
        $parts = explode(',', $this->name);

        return trim((string) ($parts[1] ?? null));
    }
}
