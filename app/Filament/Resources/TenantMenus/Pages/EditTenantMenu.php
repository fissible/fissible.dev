<?php

namespace App\Filament\Resources\TenantMenus\Pages;

use App\Filament\Resources\TenantMenus\TenantMenuResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTenantMenu extends EditRecord
{
    protected static string $resource = TenantMenuResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
