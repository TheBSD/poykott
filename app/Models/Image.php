<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    protected $fillable = ['path', 'type'];

    // image types enum

    // protected $casts = [
    //     'type' => ['image', 'logo'],
    // ];

    public function imageable()
    {
        return $this->morphTo();
    }
}
