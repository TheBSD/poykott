<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class ContactMessage extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'name',
        'email',
        'message',
        'read_at',
    ];

    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
        ];
    }

    /**
     * get boolean value for is_read to make Filament IconColumn boolean
     * shows false icon as well as true icon
     */
    protected function isRead(): Attribute
    {
        return Attribute::make(
            get: function (): bool {
                return $this->read_at !== null;
            }
        );
    }

    public function markAsRead(): void
    {
        $this->update([
            'read_at' => now(),
        ]);
    }

    public function scopeNotRead(Builder $query): Builder
    {
        return $query->whereNull('read_at');
    }
}
