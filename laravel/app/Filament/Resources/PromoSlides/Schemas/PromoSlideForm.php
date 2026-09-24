<?php

namespace App\Filament\Resources\PromoSlides\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PromoSlideForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Текст слайда')
                    ->columns(2)
                    ->schema([
                        TextInput::make('title')
                            ->label('Заголовок')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(2),

                        Textarea::make('subtitle')
                            ->label('Подзаголовок')
                            ->rows(3)
                            ->columnSpan(2),

                        TextInput::make('button_text')
                            ->label('Текст кнопки')
                            ->required()
                            ->maxLength(255)
                            ->default('Все статьи'),

                        TextInput::make('link')
                            ->label('Ссылка')
                            ->helperText('Оставьте пустым — кнопка поведёт на список статей')
                            ->maxLength(255),
                    ]),

                Section::make('Картинка')
                    ->columns(2)
                    ->schema([
                        FileUpload::make('image')
                            ->label('Главная картинка (обложка)')
                            ->helperText('Используется везде, где не задана отдельная картинка')
                            ->image()
                            ->disk('yandex')->directory('promo')
                            ->columnSpan(2),

                        FileUpload::make('image_mobile')
                            ->label('Картинка для мобильной версии')
                            ->helperText('Необязательно. Если не задана — используется главная')
                            ->image()
                            ->disk('yandex')->directory('promo'),

                        FileUpload::make('image_tablet')
                            ->label('Картинка для планшета/десктопа')
                            ->helperText('Необязательно. Если не задана — используется главная')
                            ->image()
                            ->disk('yandex')->directory('promo'),
                    ]),

                Toggle::make('is_active')
                    ->label('Показывать на главной')
                    ->helperText('Выключите — слайд исчезнет из слайдера')
                    ->default(true),
            ]);
    }
}
