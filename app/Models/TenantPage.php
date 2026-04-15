<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenantPage extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'slug',
        'title',
        'excerpt',
        'body',
        'is_homepage',
        'is_system',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'is_homepage' => 'boolean',
            'is_system' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
