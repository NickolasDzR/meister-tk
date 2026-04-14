<?php

namespace App\Filament\Resources\Posts\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class PostsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make("image")
                    ->label("Фото")
                    ->circular(),

                TextColumn::make("title")
                    ->label("Заголовок")
                    ->searchable()
                    ->sortable()
                    ->limit(50),

                IconColumn::make("published")
                    ->label("Опубликовано")
                    ->boolean(),

                TextColumn::make("published_at")
                    ->label("Дата публикации")
                    ->dateTime("d.m.Y H:i")
                    ->sortable(),

                TextColumn::make("created_at")
                    ->label("Создано")
                    ->dateTime("d.m.Y")
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make("published")
                    ->label("Опубликовано"),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort("created_at", "desc");
    }
}
