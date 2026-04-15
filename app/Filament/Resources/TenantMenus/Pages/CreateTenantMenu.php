<?php

namespace App\Filament\Resources\TenantMenus\Pages;

use App\Filament\Resources\TenantMenus\TenantMenuResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTenantMenu extends CreateRecord
{
    protected static string $resource = TenantMenuResource::class;
}
