<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class CompanyPerson extends Pivot
{
    protected $fillable = ['company_id', 'person_id', 'type'];
}
