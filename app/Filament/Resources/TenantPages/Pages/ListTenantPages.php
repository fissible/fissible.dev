<?php

namespace App\Filament\Resources\TenantPages\Pages;

use App\Filament\Resources\TenantPages\TenantPageResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTenantPages extends ListRecords
{
    protected static string $resource = TenantPageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
