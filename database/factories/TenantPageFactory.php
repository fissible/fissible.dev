<?php

namespace Database\Factories;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

class TenantPageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'slug' => fake()->slug(2),
            'title' => fake()->sentence(4),
            'excerpt' => fake()->sentence(),
            'body' => '<p>' . fake()->paragraph() . '</p>',
            'is_homepage' => false,
            'is_system' => false,
            'published_at' => now(),
        ];
    }
}
