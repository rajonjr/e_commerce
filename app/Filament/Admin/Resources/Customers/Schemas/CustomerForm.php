<?php

namespace App\Filament\Admin\Resources\Customers\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class CustomerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Customer Information')
                    ->schema([
                        TextInput::make('name')
                            ->required(),
                        TextInput::make('email')
                            ->unique(ignoreRecord: true)
                            ->label('Email address')
                            ->email()
                            ->required(),
                        DateTimePicker::make('email_verified_at'),
                        TextInput::make('phone')
                            ->default(null)
                            ->tel(),
                        DatePicker::make('date_of_birth')
                            ->native(false)
                            ->native()
                            ->displayFormat('M d, Y'),
                        Select::make('gender')
                            ->native(false)
                            ->default(null)
                            ->options(['male' => 'Male', 'female' => 'Female', 'other' => 'Other']),
                        Toggle::make('is_active')
                            ->required(),
                    ])->columns(2),
                Section::make('Password Infos')
                    ->schema([
                        TextInput::make('password')
                            ->password()
                            ->revealable()
                            ->dehydrated(fn($state) => filled($state))
                            ->required(fn($operation) => $operation === 'create')
                            ->dehydrateStateUsing(fn($state) => filled($state) ? Hash::make($state) : null),
                        TextInput::make('password_confirmation')
                            ->password()
                            ->revealable()
                            ->same('password')
                            ->required(fn($operation) => $operation === 'create'),
                    ])
            ]);
    }
}