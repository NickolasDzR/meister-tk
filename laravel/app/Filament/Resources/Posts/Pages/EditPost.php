<?php

namespace App\Filament\Resources\Posts\Pages;

use App\Filament\Resources\Posts\PostResource;
use App\Services\AnnounceChannel;
use App\Services\TelegramChannel;
use App\Services\VkWall;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Js;

class EditPost extends EditRecord
{
    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ...$this->announceActions(app(TelegramChannel::class)),
            ...$this->announceActions(app(VkWall::class)),
            DeleteAction::make(),
        ];
    }

    /**
     * Кнопки анонса для площадки. С ключами в .env — только «Отправить ещё раз»
     * после ошибок. Без ключей — ручной режим: скопировать текст и отметить
     * отправленным.
     */
    private function announceActions(AnnounceChannel $channel): array
    {
        $k = $channel->key();
        $name = $channel->name();
        $hasText = fn (): bool => filled($this->record->{"{$k}_text"});
        $sent = fn (): bool => $this->record->{"{$k}_posted_at"} !== null;

        return [
            // После трёх неудачных попыток планировщик сдаётся. Кнопка обнуляет
            // счётчик — следующий запуск (в течение минуты) попробует снова.
            Action::make("{$k}Retry")
                ->label("Отправить ещё раз: {$name}")
                ->icon("heroicon-o-paper-airplane")
                ->color("warning")
                ->visible(fn (): bool => $this->record->announceStatus($k) === "Ошибка")
                ->action(function () use ($k, $name): void {
                    $this->record->forceFill([
                        "{$k}_attempts" => 0,
                        "{$k}_error" => null,
                    ])->save();

                    Notification::make()
                        ->title("Анонс уйдёт в {$name} в течение минуты")
                        ->success()
                        ->send();
                }),

            ActionGroup::make([
                // Копируется сохранённый текст: правки в форме сначала сохранить.
                Action::make("{$k}Copy")
                    ->label("Скопировать анонс")
                    ->icon("heroicon-o-clipboard-document")
                    ->alpineClickHandler(fn (): string => "window.navigator.clipboard.writeText("
                        .Js::from($channel->manualText($this->record))
                        .").then(() => new FilamentNotification().title('Анонс скопирован').success().send())"),

                Action::make("{$k}MarkSent")
                    ->label("Отметить отправленным")
                    ->icon("heroicon-o-check")
                    ->requiresConfirmation()
                    ->modalDescription("Статус сменится на «Отправлен». Сам пост этим не публикуется.")
                    ->hidden($sent)
                    ->action(function () use ($k): void {
                        $this->record->forceFill(["{$k}_posted_at" => now()])->save();
                    }),
            ])
                ->label("Анонс: {$name}")
                ->icon("heroicon-o-megaphone")
                ->button()
                ->color("gray")
                ->visible(fn (): bool => ! $channel->isConfigured() && $hasText()),
        ];
    }
}
