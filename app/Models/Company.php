<?php

namespace App\Models;

use App\Enums\CompanyPersonType;
use App\Traits\HasImagePath;
use App\Traits\HasTags;
use App\Traits\HasTempMedia;
use App\Traits\Media\HasFileMigration;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Carbon;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Company extends Model implements HasMedia
{
    use HasFactory;
    use HasFileMigration;
    use HasImagePath;
    use HasSlug;
    use HasTags;
    use HasTempMedia;
    use InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'exit_strategy',
        'approved_at',
        'name',
        'slug',
        'description',
        'short_description',
        'notes',
        'valuation',
        'exit_valuation',
        'stock_symbol',
        'url',
        'total_funding',
        'last_funding_date',
        'funding_stage',
        'headquarter',
        'founded_at',
        'employee_count',
        'stock_quote',
    ];

    /**
     * Attributes
     */

    /**
     * @return Attribute
     *
     * The source provides only the founding year. When stored in the database,
     * * the current month and day are also recorded, which is incorrect. This method
     * * ensures we only retrieve and store the founding year.
     */
    protected function foundedAt(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value): ?string => filled($value)
                ? Carbon::parse($value)->format('Y')
                : null
        );
    }

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'exit_strategy_id' => 'integer',
            'approved_at' => 'timestamp',
            'last_funding_date' => 'date',
            'founded_at' => 'date',
            'notes' => 'collection',
        ];
    }

    /**
     * Packages configurations.
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('optimized')->optimize()->format('webp');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('default')->singleFile();
    }

    /**
     * Scopes
     */
    public function scopeApproved(Builder $query): Builder
    {
        return $query->whereNotNull('approved_at');
    }

    /**
     * Relations
     */
    public function people(): BelongsToMany
    {
        return $this->belongsToMany(Person::class)->withPivot('type');
    }

    public function founders(): BelongsToMany
    {
        return $this->belongsToMany(Person::class)->wherePivot('type', CompanyPersonType::Founder);
    }

    public function alternatives(): BelongsToMany
    {
        return $this->belongsToMany(Alternative::class);
    }

    public function investors(): BelongsToMany
    {
        return $this->belongsToMany(Investor::class);
    }

    public function resources(): MorphMany
    {
        return $this->morphMany(Resource::class, 'resourceable');
    }

    public function officeLocations(): BelongsToMany
    {
        return $this->belongsToMany(OfficeLocation::class);
    }

    public function socialLinks(): MorphMany
    {
        return $this->morphMany(SocialLink::class, 'linkable');
    }

    /**
     * Methods
     */
    public function hasOfficeLocation(OfficeLocation $officeLocation): bool
    {
        return $this->officeLocations()->where('office_location_id', $officeLocation->id)->exists();
    }

    public function doesntHaveOfficeLocation(OfficeLocation $officeLocation): bool
    {
        return ! $this->hasOfficeLocation($officeLocation);
    }
}
