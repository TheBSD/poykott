<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $category_id
 * @property int $exit_strategy_id
 * @property int $funding_level_id
 * @property int $company_size_id
 * @property \Carbon\Carbon $approved_at
 * @property string $name
 * @property string $slug
 * @property string $description
 * @property string $logo
 * @property string $notes
 * @property int $valuation
 * @property int $exit_valuation
 * @property string $stock_symbol
 * @property string $url
 * @property int $total_funding
 * @property \Carbon\Carbon $last_funding_date
 * @property string $headquarter
 * @property \Carbon\Carbon $founded_at
 * @property string $office_locations
 * @property int $employee_count
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Company extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'category_id',
        'exit_strategy_id',
        'funding_level_id',
        'company_size_id',
        'approved_at',
        'name',
        'slug',
        'description',
        'logo',
        'notes',
        'valuation',
        'exit_valuation',
        'stock_symbol',
        'url',
        'total_funding',
        'last_funding_date',
        'headquarter',
        'founded_at',
        'office_locations',
        'employee_count',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'category_id' => 'integer',
        'exit_strategy_id' => 'integer',
        'funding_level_id' => 'integer',
        'company_size_id' => 'integer',
        'approved_at' => 'timestamp',
        'last_funding_date' => 'date',
        'founded_at' => 'date',
        'office_locations' => 'array',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function exitStrategy(): BelongsTo
    {
        return $this->belongsTo(ExitStrategy::class);
    }

    public function companySize(): BelongsTo
    {
        return $this->belongsTo(CompanySize::class);
    }

    public function fundingLevel(): BelongsTo
    {
        return $this->belongsTo(FundingLevel::class);
    }

    public function people(): BelongsToMany
    {
        return $this->belongsToMany(Person::class);
    }

    public function alternatives(): BelongsToMany
    {
        return $this->belongsToMany(Alternative::class);
    }

    public function companyResources(): HasMany
    {
        return $this->hasMany(CompanyResources::class);
    }
}
