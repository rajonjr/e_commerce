<?php

namespace App\Filament\Admin\Resources\Orders\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Order Status')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        Select::make('status')
                            ->label('Order Status')
                            ->options([
                                'pending' => 'Pending',
                                'processing' => 'Processing',
                                'striped' => 'Striped',
                                'delivered' => 'Delivered',
                                'cancelled' => 'Cancelled',
                            ])
                            ->default('pending')
                            ->native(false)
                            ->required(),
                        TextInput::make('tracking_number')
                            ->default(null)
                            ->helperText('Shipping tracking number'),
                        Select::make('payment_status')
                            ->options([
                                'pending' => 'Pending',
                                'paid' => 'Paid',
                                'failed' => 'Failed',
                                'refunded' => 'Refunded',
                            ])
                            ->native(false)
                            ->required()
                            ->default('pending'),
                        Textarea::make('admin_notes')
                            ->default(null)
                            ->columnSpanFull(),
                    ]),
                TextInput::make('order_number')
                    ->required(),
                TextInput::make('customer_id')
                    ->required()
                    ->numeric(),
                TextInput::make('coupon_id')
                    ->numeric(),
                TextInput::make('subtotal')
                    ->required()
                    ->numeric(),
                TextInput::make('discount_amount')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('shipping_cost')
                    ->required()
                    ->numeric()
                    ->default(0.0)
                    ->prefix('$'),
                TextInput::make('tax_amount')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('total')
                    ->required()
                    ->numeric(),
                TextInput::make('shipping_full_name')
                    ->required(),
                TextInput::make('shipping_phone')
                    ->tel()
                    ->required(),
                TextInput::make('shipping_address_line_1')
                    ->required(),
                TextInput::make('shipping_address_line_2'),
                TextInput::make('shipping_city')
                    ->required(),
                TextInput::make('shipping_state'),
                TextInput::make('shipping_postal_code')
                    ->required(),
                TextInput::make('shipping_country')
                    ->required(),
                Select::make('payment_method')
                    ->options(['stripe' => 'Stripe', 'cash_on_delivery' => 'Cash on delivery'])
                    ->default('stripe')
                    ->required(),
                TextInput::make('transaction_id'),
                Textarea::make('customer_notes')
                    ->columnSpanFull(),
            ]);
    }
}