<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenantMenu extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'location',
        'label',
        'items',
    ];

    protected function casts(): array
    {
        return [
            'items' => 'array',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
