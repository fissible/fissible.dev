<?php

namespace App\Filament\Resources\TenantMenus;

use App\Filament\Resources\TenantMenus\Pages\CreateTenantMenu;
use App\Filament\Resources\TenantMenus\Pages\EditTenantMenu;
use App\Filament\Resources\TenantMenus\Pages\ListTenantMenus;
use App\Filament\Resources\TenantMenus\Schemas\TenantMenuForm;
use App\Filament\Resources\TenantMenus\Tables\TenantMenusTable;
use App\Models\TenantMenu;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TenantMenuResource extends Resource
{
    protected static ?string $model = TenantMenu::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return TenantMenuForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TenantMenusTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTenantMenus::route('/'),
            'create' => CreateTenantMenu::route('/create'),
            'edit' => EditTenantMenu::route('/{record}/edit'),
        ];
    }
}
