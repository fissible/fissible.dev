<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Tenant extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::creating(function (Tenant $tenant) {
            $tenant->uuid ??= (string) Str::uuid();
        });
    }

    protected $fillable = [
        'uuid',
        'name',
        'slug',
        'domain',
        'status',
        'is_demo',
        'demo_expires_at',
        'settings',
    ];

    protected function casts(): array
    {
        return [
            'is_demo' => 'boolean',
            'demo_expires_at' => 'datetime',
            'settings' => 'array',
        ];
    }

    public function memberships(): HasMany
    {
        return $this->hasMany(TenantMembership::class);
    }

    public function pages(): HasMany
    {
        return $this->hasMany(TenantPage::class);
    }

    public function menus(): HasMany
    {
        return $this->hasMany(TenantMenu::class);
    }
}
