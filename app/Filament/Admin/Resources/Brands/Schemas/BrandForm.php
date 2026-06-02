<?php

namespace App\Filament\Admin\Resources\Brands\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BrandForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Brand Information')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('name')
                            ->required(),
                        TextInput::make('slug')
                            ->visibleOn('edit')
                            ->readOnly()
                            ->unique(ignoreRecord: true)
                            ->required(),
                        Textarea::make('description')
                            ->rows(3)
                            ->default(null)
                            ->columnSpanFull(),
                        FileUpload::make('logo')
                            ->directory('brands')
                            ->maxSize(2048)
                            ->image()
                            ->imageEditor()
                            ->default(null),
                        TextInput::make('website')
                            ->url()
                            ->placeholder('https://example.com'),
                    ]),
                Section::make('Display Settings')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        Toggle::make('is_active')
                            ->required(),
                        TextInput::make('sort_order')
                            ->required()
                            ->numeric()
                            ->default(0),
                    ])
            ]);
    }
}