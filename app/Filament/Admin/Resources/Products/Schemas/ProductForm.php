<?php

namespace App\Filament\Admin\Resources\Products\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Str;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
        ->components([
            Tabs::make('Product Details')
                ->columnSpanFull()
                ->tabs([
                    Tab::make('Basic Information')
                        ->icon(Heroicon::InformationCircle)
                        ->schema([
                            Section::make('Product Details')
                                ->schema([
                                    TextInput::make('name')
                                        ->required(),
                                    TextInput::make('slug')
                                        ->unique(ignoreRecord: true)
                                        ->visible(fn($operation) => $operation === 'edit')
                                        ->required(),
                                    Select::make('category_id')
                                        ->relationship('category', 'name')
                                        ->searchable()
                                        ->preload()
                                        ->required()
                                        ->createOptionForm([
                                            TextInput::make('name')
                                                ->required(),
                                            TextInput::make('slug')
                                                ->unique(ignoreRecord: true)
                                                ->readOnly()
                                                ->visibleOn('edit'),
                                        ]),
                                    Select::make('brand_id')
                                        ->relationship('brand', 'name')
                                        ->searchable()
                                        ->required()
                                        ->preload()
                                        ->createOptionForm([
                                            TextInput::make('name')
                                                ->required(),
                                            TextInput::make('slug')
                                                ->unique(ignoreRecord: true)
                                                ->readOnly()
                                                ->visibleOn('edit'),
                                        ]),
                                ])->columns(2),
                            Section::make('Product Description')
                                ->schema([
                                    Textarea::make('sort_description')
                                        ->columnSpanFull(),
                                    RichEditor::make('description')
                                        ->columnSpanFull(),
                                ]),
                        ]),
                    Tab::make('Pricing & Inventory')
                        ->icon(Heroicon::CurrencyDollar)
                        ->schema([
                            Section::make('Pricing')
                                ->schema([
                                    TextInput::make('sku')
                                        ->label('SKU')
                                        ->required()
                                        ->unique(ignoreRecord: true)
                                        ->helperText('Stock Keeping Unit - unique identifier')
                                        ->default(fn() => 'SKU-' . strtoupper(Str::random(8))),
                                    TextInput::make('price')
                                        ->required()
                                        ->minValue(0)
                                        ->step(0.01)
                                        ->numeric()
                                        ->helperText('Selling Price')
                                        ->prefix('$'),
                                    TextInput::make('compare_price')
                                        ->numeric()
                                        ->minValue(0)
                                        ->step(0.01)
                                        ->helperText('Original price to show discount')
                                        ->prefix('$'),
                                    TextInput::make('cost_price')
                                        ->numeric()
                                        ->minValue(0)
                                        ->step(0.01)
                                        ->helperText('cost from supplier (for profit calculations)')
                                        ->prefix('$'),
                                ])->columns(2),
                            Section::make('Inventory')
                                ->schema([
                                    Toggle::make('manage_stock')
                                        ->default(true)
                                        ->helperText('Enable stock management for this product')
                                        ->live(),
                                    TextInput::make('stock_quantity')
                                        ->label('Stock Quantity')
                                        ->required(fn(callable $get) => $get('manage_stock'))
                                        ->disabled(fn(callable $get) => !$get('manage_stock'))
                                        ->numeric()
                                        ->default(0),
                                    TextInput::make('low_stock_threshold')
                                        ->default(0)
                                        ->minValue(0)
                                        ->numeric()
                                        ->label('Low Stock Alert Threshold')
                                        ->helperText('Get notified when stock fail below this number'),
                                    Select::make('stock_status')
                                        ->required()
                                        ->options([
                                            'in_stock' => 'In Stock',
                                            'out_of_stock' => 'Out Of Stock',
                                            'on_backorder' => 'On Backorder',
                                        ])
                                        ->native(false)
                                        ->default('in_stock'),
                                    TextInput::make('weight')
                                        ->numeric()
                                        ->label('weight (kg)')
                                        ->minValue(0)
                                        ->helperText('use for shipping calculations'),
                                ])->columns(2),
                        ]),
                    Tab::make('Images')
                        ->icon(Heroicon::Photo)
                        ->schema([
                            Section::make('Product Images')
                                ->description('Upload multiple image.The first images will be the primary image.')
                                ->schema([
                                    FileUpload::make('image_path')
                                        ->label('Product Images')
                                        ->multiple()
                                        ->image()
                                        ->disk('public')
                                        ->directory('products')
                                        ->imageEditor()
                                        ->maxSize(2048)
                                        ->reorderable()
                                        ->columnSpanFull()
                                        ->helperText('You can drag and drop to reorder images')
                                        ->saveRelationshipsUsing(function ($components, $state, $record) {
                                            // Delete existing images
                                            $record->images()->delete();

                                            if (is_array($state)) {
                                                foreach ($state as $index => $imagePath) {
                                                    $record->images()->create([
                                                        'image_path' => $imagePath,
                                                        'is_primary' => $index === 0,
                                                        'sort_order' => $index,
                                                    ]);
                                                }
                                            }
                                        })
                                        ->dehydrated(false),
                                ]),
                        ]),
                    Tab::make('Product variants')
                        ->icon(Heroicon::Squares2x2)
                        ->schema([
                            Toggle::make('has_variants')
                                ->required()
                                ->live(),
                            Section::make('Product Variants')
                                ->schema([
                                    Repeater::make('variants')
                                        ->relationship('variants')
                                        ->schema([
                                            TextInput::make('name')
                                                ->required()
                                                ->label('Variant Name')
                                                ->placeholder('e.g: Red - Large'),
                                            KeyValue::make('options'),
                                            TextInput::make('sku')
                                                ->label('SKU')
                                                ->required()
                                                ->unique(ignoreRecord: true)
                                                ->helperText('Stock Keeping Unit - unique identifier')
                                                ->default(fn() => 'VAR-' . strtoupper(Str::random(8)))
                                                ->columnSpan(2),
                                            TextInput::make('price')
                                                ->required()
                                                ->minValue(0)
                                                ->step(0.01)
                                                ->numeric()
                                                ->helperText('Selling Price')
                                                ->prefix('$'),
                                            TextInput::make('compare_price')
                                                ->numeric()
                                                ->minValue(0)
                                                ->step(0.01)
                                                ->helperText('Original price to show discount')
                                                ->prefix('$'),
                                            TextInput::make('stock_quantity')
                                                ->label('Stock')
                                                ->required()
                                                ->disabled()
                                                ->numeric()
                                                ->minValue(0)
                                                ->default(0),
                                            Select::make('stock_status')
                                                ->required()
                                                ->options([
                                                    'in_stock' => 'In Stock',
                                                    'out_of_stock' => 'Out Of Stock',
                                                    'on_backorder' => 'On Backorder',
                                                ])
                                                ->native(false)
                                                ->default('in_stock'),
                                            Toggle::make('is_active')
                                                ->required()
                                                ->default(true),
                                        ])
                                        ->columns(2)
                                        ->defaultItems(0)
                                        ->collapsible()
                                        ->itemLabel(fn(array $state): ?string => $state['name'] ?? null)
                                        ->addActionLabel('Add Variant'),
                                ])
                                ->columnSpanFull()
                                ->visible(fn(callable $get) => $get('has_variants')),
                        ]),
                    // Settings
                    Tab::make('Settings')
                        ->icon(Heroicon::Cog6Tooth)
                        ->schema([
                            Section::make('Product Status')
                                ->schema([
                                    Toggle::make('is_active')
                                        ->required()
                                        ->default(true),
                                    Toggle::make('is_featured')
                                        ->required(),
                                ])
                                ->columns(2),
                            Section::make('Statistics')
                                ->schema([
                                    Placeholder::make('views_count')
                                        ->content(fn($record) => $record?->views_count ?? 0),
                                    Placeholder::make('created_at')
                                        ->label('Created')
                                        ->content(fn($record) => $record?->created_at?->diffForHumans() ?? '-'),
                                ]),
                        ]),
                    Tab::make('SEO')
                        ->icon(Heroicon::MagnifyingGlass)
                        ->schema([
                            Section::make('Search Engine Optimazation')
                                ->schema([
                                    TextInput::make('meta_title'),
                                    Textarea::make('meta_description')
                                        ->columnSpanFull(),
                                ]),
                        ]),

                ]),
        ]);
    }
}