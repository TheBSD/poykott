<?php

namespace App\Models;

use App\Enums\CompanyPersonType;
use Illuminate\Database\Eloquent\Relations\Pivot;
use OwenIt\Auditing\Contracts\Auditable;

class CompanyPerson extends Pivot implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $fillable = ['company_id', 'person_id', 'type'];

    protected function casts(): array
    {
        return [
            'type' => CompanyPersonType::class,
        ];
    }
}
