<?php

namespace App\Filament\Resources\Posts\Schemas;

use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->columns(2)
                    ->schema([
                        TextInput::make("title")
                            ->label("Заголовок")
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (string $operation, $state, callable $set) =>
                                $operation === "create" ? $set("slug", \Illuminate\Support\Str::slug($state)) : null
                            )
                            ->columnSpan(2),

                        TextInput::make("slug")
                            ->label("Slug (URL)")
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->columnSpan(2),

                        Textarea::make("excerpt")
                            ->label("Краткое описание")
                            ->rows(3)
                            ->columnSpan(2),

                        FileUpload::make("image")
                            ->label("Обложка")
                            ->image()
                            ->disk("yandex")->directory("posts")
                            ->columnSpan(2),

                        Toggle::make("published")
                            ->label("Опубликовано")
                            ->default(false),

                        DateTimePicker::make("published_at")
                            ->label("Дата публикации")
                            ->nullable(),
                    ]),

                Section::make("Содержимое")
                    ->schema([
                        Builder::make("content")
                            ->label("")
                            ->blocks([

                                // 1. Текст
                                Block::make("text")
                                    ->label("Текст")
                                    ->icon("heroicon-o-document-text")
                                    ->schema([
                                        RichEditor::make("content")
                                            ->label("Текст")
                                            ->toolbarButtons(["bold", "italic", "link", "bulletList", "orderedList", "undo", "redo"])
                                            ->required(),
                                    ]),

                                // 2. Картинка + Текст
                                Block::make("image_with_text")
                                    ->label("Картинка + Текст")
                                    ->icon("heroicon-o-photo")
                                    ->schema([
                                        FileUpload::make("image")
                                            ->label("Картинка (слева)")
                                            ->image()
                                            ->disk("yandex")->directory("posts/content")
                                            ->required(),
                                        Textarea::make("text")
                                            ->label("Текст (справа)")
                                            ->rows(5)
                                            ->required(),
                                    ]),

                                // 3. Заголовок h2
                                Block::make("heading")
                                    ->label("Заголовок h2")
                                    ->icon("heroicon-o-h2")
                                    ->schema([
                                        TextInput::make("text")
                                            ->label("Текст заголовка")
                                            ->required(),
                                    ]),

                                // 4. Цитата
                                Block::make("quote")
                                    ->label("Цитата")
                                    ->icon("heroicon-o-chat-bubble-left")
                                    ->schema([
                                        Textarea::make("text")
                                            ->label("Текст цитаты")
                                            ->rows(3)
                                            ->required(),
                                        TextInput::make("author")
                                            ->label("Автор (необязательно)")
                                            ->maxLength(255),
                                    ]),

                                // 5. Маркированный список
                                Block::make("unordered_list")
                                    ->label("Список маркированный")
                                    ->icon("heroicon-o-list-bullet")
                                    ->schema([
                                        Repeater::make("items")
                                            ->label("Пункты")
                                            ->schema([
                                                TextInput::make("text")
                                                    ->label("Пункт")
                                                    ->required(),
                                            ])
                                            ->addActionLabel("Добавить пункт")
                                            ->reorderable()
                                            ->minItems(1),
                                    ]),

                                // 6. Нумерованный список
                                Block::make("ordered_list")
                                    ->label("Список нумерованный")
                                    ->icon("heroicon-m-numbered-list")
                                    ->schema([
                                        Repeater::make("items")
                                            ->label("Пункты")
                                            ->schema([
                                                TextInput::make("text")
                                                    ->label("Пункт")
                                                    ->required(),
                                            ])
                                            ->addActionLabel("Добавить пункт")
                                            ->reorderable()
                                            ->minItems(1),
                                    ]),

                                // 7. Две картинки
                                Block::make("two_images")
                                    ->label("Две картинки")
                                    ->icon("heroicon-o-squares-2x2")
                                    ->schema([
                                        FileUpload::make("image_left")
                                            ->label("Картинка слева")
                                            ->image()
                                            ->disk("yandex")->directory("posts/content")
                                            ->required(),
                                        FileUpload::make("image_right")
                                            ->label("Картинка справа")
                                            ->image()
                                            ->disk("yandex")->directory("posts/content")
                                            ->required(),
                                    ]),

                                // 8. Врезка
                                Block::make("callout")
                                    ->label("Врезка")
                                    ->icon("heroicon-o-bookmark")
                                    ->schema([
                                        TextInput::make("title")
                                            ->label("Заголовок врезки (необязательно)")
                                            ->maxLength(255),
                                        Textarea::make("text")
                                            ->label("Текст врезки")
                                            ->rows(3)
                                            ->required(),
                                    ]),

                                // 9. Курсив (em)
                                Block::make("italic")
                                    ->label("Курсив (em)")
                                    ->icon("heroicon-o-italic")
                                    ->schema([
                                        Textarea::make("text")
                                            ->label("Текст курсивом")
                                            ->rows(2)
                                            ->required(),
                                    ]),

                                // 10. Разделитель (hr)
                                Block::make("divider")
                                    ->label("Разделитель (hr)")
                                    ->icon("heroicon-o-minus")
                                    ->schema([
                                        Placeholder::make("divider_hint")
                                            ->content("— Горизонтальная линия —"),
                                    ]),

                            ])
                            ->addActionLabel("Добавить блок")
                            ->reorderable()
                            ->collapsible()
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
