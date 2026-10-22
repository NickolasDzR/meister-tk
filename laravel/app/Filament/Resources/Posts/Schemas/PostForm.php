<?php

namespace App\Filament\Resources\Posts\Schemas;

use App\Models\Post;
use App\Services\AnnounceChannel;
use App\Services\TelegramChannel;
use App\Services\VkWall;
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
use Filament\Schemas\Components\Utilities\Get;
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
                            ->helperText("Показывается в карточке статьи, в слайдере на главной и в превью ссылки в мессенджерах. Оптимально 150–200 символов — длиннее везде обрезается.")
                            ->required()
                            ->maxLength(300)
                            ->rows(3)
                            ->columnSpan(2),

                        FileUpload::make("image")
                            ->label("Главная картинка (обложка)")
                            ->helperText("Используется везде, где не задана отдельная картинка для мобильной/планшетной версии")
                            ->image()
                            ->disk("yandex")->directory("posts")
                            ->required()
                            ->columnSpan(2),

                        FileUpload::make("image_mobile")
                            ->label("Картинка для мобильной версии")
                            ->helperText("Необязательно. Если не задана — используется главная картинка")
                            ->image()
                            ->disk("yandex")->directory("posts")
                            ->columnSpan(1),

                        FileUpload::make("image_tablet")
                            ->label("Картинка для планшета/десктопа")
                            ->helperText("Необязательно. Если не задана — используется главная картинка")
                            ->image()
                            ->disk("yandex")->directory("posts")
                            ->columnSpan(1),

                        FileUpload::make("og_image")
                            ->label("Картинка для превью ссылки")
                            ->helperText("JPG 1200×630. Телеграм и ВК плохо показывают webp в превью, поэтому для них отдельный файл. Пусто — возьмётся главная картинка.")
                            ->image()
                            ->disk("yandex")->directory("posts/og")
                            ->columnSpan(2),

                        Toggle::make("published")
                            ->label("Опубликовано")
                            ->helperText("Выключено — черновик, на сайте не виден никак")
                            ->default(false),

                        DateTimePicker::make("published_at")
                            ->label("Дата публикации")
                            ->helperText("Пусто — поставится текущее время. Дата в будущем — статья появится на сайте в этот момент, а до тех пор будет помечена как запланированная.")
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
                                        Toggle::make("zoomable")
                                            ->label("Можно раскрыть по клику")
                                            ->helperText("Включите для скриншотов и схем с мелкими подписями.")
                                            ->default(false),
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
                                        Toggle::make("zoomable")
                                            ->label("Можно раскрыть по клику")
                                            ->helperText("Включите для скриншотов и схем с мелкими подписями.")
                                            ->default(false),
                                    ]),

                                // 8. Врезка
                                Block::make("callout")
                                    ->label("Врезка")
                                    ->icon("heroicon-o-bookmark")
                                    ->schema([
                                        Textarea::make("text")
                                            ->label("Текст врезки")
                                            ->rows(3)
                                            ->required(),
                                    ]),

                                // 9. Ремарка
                                Block::make("italic")
                                    ->label("Ремарка")
                                    ->icon("heroicon-o-italic")
                                    ->schema([
                                        Textarea::make("text")
                                            ->label("Текст ремарки")
                                            ->helperText("Замечание в сторону от основного рассказа — целиком курсивом. Чтобы выделить слово внутри абзаца, используйте курсив в блоке «Текст».")
                                            ->rows(2)
                                            ->required(),
                                    ]),

                                // 10. Разделитель (hr)
                                Block::make("divider")
                                    ->label("Разделитель (hr)")
                                    ->icon("heroicon-o-minus")
                                    ->schema([
                                        Placeholder::make("divider_hint")
                                            ->content("- Горизонтальная линия -"),
                                    ]),

                            ])
                            ->addActionLabel("Добавить блок")
                            ->reorderable()
                            ->collapsible()
                            ->columnSpanFull(),
                    ]),

                self::announceSection(app(TelegramChannel::class), 900, "Обычный текст. **Так** — жирным. Обложка и ссылка на статью добавятся сами."),

                self::announceSection(app(VkWall::class), 2000, "Обычный текст, без жирного. Ссылка на статью с карточкой и обложкой добавится сама."),
            ]);
    }

    /**
     * Раздел анонса для площадки: тумблер автоотправки, текст, статус.
     * Нет ключей в .env — тумблера нет: анонс копируют кнопкой вверху
     * страницы и публикуют руками.
     */
    private static function announceSection(AnnounceChannel $channel, int $maxLength, string $textHelp): Section
    {
        $k = $channel->key();
        $auto = $channel->isConfigured();

        return Section::make("Анонс: {$channel->name()}")
            ->description($auto
                ? "С включённым тумблером уходит сам, когда статья появляется на сайте: сразу после публикации или в запланированное время."
                : "Автоотправка выключена. Сохраните текст, скопируйте его кнопкой «Скопировать анонс» вверху страницы и опубликуйте вручную.")
            ->collapsible()
            ->schema([
                Toggle::make("{$k}_enabled")
                    ->label("Отправить анонс: {$channel->name()}")
                    ->helperText("Выключено — пост можно спокойно писать и править, никуда ничего не уйдёт.")
                    ->default(false)
                    ->live()
                    ->visible($auto),

                Textarea::make("{$k}_text")
                    ->label("Текст поста")
                    ->helperText($textHelp." После отправки правки на площадку уже не попадут.")
                    ->required(fn (Get $get): bool => $auto && (bool) $get("{$k}_enabled"))
                    ->maxLength($maxLength)
                    ->rows(10),

                Placeholder::make("{$k}_status")
                    ->label("Статус")
                    ->content(fn (?Post $record): string => match ($record?->announceStatus($k)) {
                        "Отправлен" => ($record->{"{$k}_message_id"} ? "Отправлен " : "Отмечен отправленным вручную ")
                            .$record->{"{$k}_posted_at"}->format("d.m.Y в H:i"),
                        "Ошибка" => "Не отправлен: ".$record->{"{$k}_error"}.". Исправьте и нажмите «Отправить ещё раз» вверху страницы.",
                        "Ждёт" => $record->{"{$k}_error"}
                            ? "Ждёт отправки. Прошлая попытка: ".$record->{"{$k}_error"}
                            : "Ждёт отправки",
                        default => "—",
                    })
                    ->visibleOn("edit"),
            ]);
    }
}
