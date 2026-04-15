<?php

namespace App\Filament\Resources\TenantPages;

use App\Filament\Resources\TenantPages\Pages\CreateTenantPage;
use App\Filament\Resources\TenantPages\Pages\EditTenantPage;
use App\Filament\Resources\TenantPages\Pages\ListTenantPages;
use App\Filament\Resources\TenantPages\Schemas\TenantPageForm;
use App\Filament\Resources\TenantPages\Tables\TenantPagesTable;
use App\Models\TenantPage;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TenantPageResource extends Resource
{
    protected static ?string $model = TenantPage::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return TenantPageForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TenantPagesTable::configure($table);
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
            'index' => ListTenantPages::route('/'),
            'create' => CreateTenantPage::route('/create'),
            'edit' => EditTenantPage::route('/{record}/edit'),
        ];
    }
}
