<?php

namespace App\Filament\Admin\Resources\Reviews\Tables;

use App\Filament\Admin\Resources\Customers\CustomerResource;
use App\Filament\Admin\Resources\Products\ProductResource;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Notifications\Action;

class ReviewsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product.name')
                ->searchable()
                ->url(fn($record) => ProductResource::getUrl('edit', [$record->product]))
                ->weight('bold')
                ->sortable(),
            TextColumn::make('customer.name')
                ->searchable()
                ->url(fn($record) => CustomerResource::getUrl('edit', [$record->customer]))
                ->weight('bold')
                ->sortable(),
            TextColumn::make('rating')
                ->formatStateUsing(fn($state) => str_repeat('⭐', $state))
                ->color('warning')
                ->sortable(),
            TextColumn::make('title')
                ->searchable()
                ->limit(50),
            TextColumn::make('comment')
                ->searchable()
                ->wrap()
                ->limit(100),
            IconColumn::make('is_verified_purchase')
                ->boolean(),
            IconColumn::make('is_approved')
                ->boolean(),
            TextColumn::make('created_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('updated_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_approved')
                    ->label('Approved Status')
                    ->boolean()
                    ->trueLabel('Aprroved only')
                    ->falseLabel('Pending only')
                    ->native(false),
                TernaryFilter::make('is_verified_purchase')
                    ->label('Verified Purchase')
                    ->boolean()
                    ->trueLabel('Approved only')
                    ->falseLabel('Unverified only')
                    ->native(false)
            ])
            ->recordActions([
            Action::make('approve')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->action(fn($record) => $record->update(['is_approved', true]))
                ->visible(fn($record) => !$record->is_approved)
                ->requiresConfirmation(),
            Action::make('reject')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->action(fn($record) => $record->update(['is_approved', false]))
                ->visible(fn($record) => $record->is_approved)
                ->requiresConfirmation(),
            EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}