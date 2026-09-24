<?php

namespace App\Filament\Resources\PromoSlides\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PromoSlidesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label('Картинка')
                    ->disk('yandex')
                    ->checkFileExistence(false),

                TextColumn::make('title')
                    ->label('Заголовок')
                    ->searchable()
                    ->limit(50),

                TextColumn::make('button_text')
                    ->label('Кнопка'),

                IconColumn::make('is_active')
                    ->label('На главной')
                    ->boolean(),

                TextColumn::make('updated_at')
                    ->label('Изменён')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('updated_at', 'desc');
    }
}
