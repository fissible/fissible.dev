<?php

namespace App\Filament\Resources\TenantPages\Pages;

use App\Filament\Resources\TenantPages\TenantPageResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTenantPage extends CreateRecord
{
    protected static string $resource = TenantPageResource::class;
}
