<?php

namespace App\Filament\Resources\TenantMenus\Pages;

use App\Filament\Resources\TenantMenus\TenantMenuResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTenantMenus extends ListRecords
{
    protected static string $resource = TenantMenuResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
