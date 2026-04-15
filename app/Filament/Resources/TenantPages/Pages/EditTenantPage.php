<?php

namespace App\Filament\Resources\TenantPages\Pages;

use App\Filament\Resources\TenantPages\TenantPageResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTenantPage extends EditRecord
{
    protected static string $resource = TenantPageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
