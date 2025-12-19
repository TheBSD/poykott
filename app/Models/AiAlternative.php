<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiAlternative extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id', 'content', 'model_used', 'prompt_tokens', 'completion_tokens',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
