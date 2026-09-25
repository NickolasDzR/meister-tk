<?php

namespace App\Filament\Resources\Posts\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use App\Models\Post;
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
                    ->disk("yandex")
                    ->checkFileExistence(false)
                    ->circular(),

                TextColumn::make("title")
                    ->label("Заголовок")
                    ->searchable()
                    ->sortable()
                    ->limit(50),

                TextColumn::make("status")
                    ->label("Статус")
                    ->badge()
                    ->state(fn (Post $record): string => match (true) {
                        ! $record->published => "Черновик",
                        $record->isScheduled() => "Запланирована",
                        default => "Опубликована",
                    })
                    ->color(fn (string $state): string => match ($state) {
                        "Черновик" => "gray",
                        "Запланирована" => "warning",
                        default => "success",
                    })
                    ->tooltip(fn (Post $record): ?string => $record->isScheduled()
                        ? "Появится на сайте ".$record->published_at->format("d.m.Y в H:i")
                        : null),

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
