<?php

namespace App\Filament\Resources\TenantMenus\Schemas;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TenantMenuForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->relationship('tenant', 'name')
                    ->required()
                    ->searchable(),
                TextInput::make('location')
                    ->required()
                    ->maxLength(255),
                TextInput::make('label')
                    ->required()
                    ->maxLength(255),
                KeyValue::make('items')
                    ->keyLabel('Label')
                    ->valueLabel('URL')
                    ->columnSpanFull(),
            ]);
    }
}
