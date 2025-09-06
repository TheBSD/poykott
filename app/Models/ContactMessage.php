<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class ContactMessage extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'name',
        'email',
        'message',
        'read_at',
        'spam_at',
        'ip_address',
    ];

    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
            'spam_at' => 'datetime',
        ];
    }

    /**
     * =====================
     *  Attributes
     * =====================
     */

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

    protected function isSpam(): Attribute
    {
        return Attribute::make(
            get: function (): bool {
                return $this->spam_at !== null;
            }
        );
    }

    /**
     * =====================
     *  Scopes
     * =====================
     */
    public function scopeNotRead(Builder $query): Builder
    {
        return $query->whereNull('read_at');
    }

    public function scopeSpam(Builder $query): Builder
    {
        return $query->whereNotNull('spam_at');
    }

    public function scopeNotSpam(Builder $query): Builder
    {
        return $query->whereNull('spam_at');
    }

    /**
     * =====================
     * Methods
     * =====================
     */
    public function markAsRead(): void
    {
        $this->update([
            'read_at' => now(),
        ]);
    }

    public function markAsSpam(): void
    {
        $this->update([
            'spam_at' => now(),
        ]);
    }

    public function markAsNotSpam(): void
    {
        $this->update([
            'spam_at' => null,
        ]);
    }
}
