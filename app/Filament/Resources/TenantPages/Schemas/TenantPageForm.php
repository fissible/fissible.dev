<?php

namespace App\Filament\Resources\TenantPages\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class TenantPageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->relationship('tenant', 'name')
                    ->required()
                    ->searchable(),
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                TextInput::make('slug')
                    ->required()
                    ->maxLength(255),
                Textarea::make('excerpt')
                    ->rows(2),
                RichEditor::make('body')
                    ->columnSpanFull(),
                Toggle::make('is_homepage')
                    ->default(false),
                Toggle::make('is_system')
                    ->default(false),
                DateTimePicker::make('published_at'),
            ]);
    }
}
