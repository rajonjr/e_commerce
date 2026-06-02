<?php

namespace App\Filament\Admin\Resources\Orders\Tables;

use App\Filament\Admin\Resources\Customers\CustomerResource;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_number')
                ->searchable()
                ->sortable()
                ->weight('bold')
                ->copyable(),
            TextColumn::make('customer.name')
                ->searchable()
                ->url(fn($record) => $record->customer ? CustomerResource::getUrl('edit', [$record->customer]) : null)
                ->sortable(),
            TextColumn::make('coupon_id')
                ->numeric()
                ->sortable(),
            TextColumn::make('discount_amount')
                ->numeric()
                ->sortable(),
            TextColumn::make('shipping_cost')
                ->money()
                ->sortable(),
            TextColumn::make('tax_amount')
                ->numeric()
                ->sortable(),
            TextColumn::make('total')
                ->money('USD')
                ->color('success')
                ->weight('bold')
                ->sortable(),
            TextColumn::make('payment_status')
                ->searchable()
                ->badge(),
            TextColumn::make('status')
                ->badge(),
            TextColumn::make('items_count')
                ->badge()
                ->counts('items')
                ->color('info'),
            TextColumn::make('tracking_number')
                ->searchable()
                ->toggleable()
                ->copyable(),
            TextColumn::make('created_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('updated_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('deleted_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}