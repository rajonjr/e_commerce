<?php

namespace App\Filament\Admin\Resources\Reviews\Schemas;

use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ReviewForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Moderation')
                    ->schema([
                        Toggle::make('is_approved')
                            ->label('Approve review')
                            ->helperText('Approved review will be visible on product page')
                            ->required(),
                    ]),
            ]);
    }
}