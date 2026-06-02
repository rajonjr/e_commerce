<?php

namespace App\Filament\Admin\Resources\Categories\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Category Information')
                    ->schema([
                        TextInput::make('name')
                            ->required(),
                        TextInput::make('slug')
                            ->readOnly()
                            ->unique(ignoreRecord: true)
                            ->visibleOn('edit'),
                        Textarea::make('description')
                            ->rows(3)
                            ->default(null)
                            ->columnSpanFull(),
                        FileUpload::make('image')
                            ->directory('categories')
                            ->imageEditor()
                            ->preserveFilenames()
                            ->downloadable()
                            ->image(),
                    ])->columnSpanFull()
                    ->columns(2),
                Section::make('Display Settings')
                    ->schema([
                        Toggle::make('is_active')
                            ->required(),
                        TextInput::make('sort_order')
                            ->required()
                            ->numeric()
                            ->default(0),
                    ])->columns(2),
                Section::make('SEO')
                    ->schema([
                        TextInput::make('meta_title'),
                        Textarea::make('meta_description')
                            ->columnSpanFull(),
                    ])
            ]);
    }
}