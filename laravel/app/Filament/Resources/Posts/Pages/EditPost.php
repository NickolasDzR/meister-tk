<?php

namespace App\Filament\Resources\Posts\Pages;

use App\Filament\Resources\Posts\PostResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditPost extends EditRecord
{
    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // После трёх неудачных попыток планировщик сдаётся. Кнопка обнуляет
            // счётчик — следующий запуск (в течение минуты) попробует снова.
            Action::make("retryTelegram")
                ->label("Отправить в Telegram ещё раз")
                ->icon("heroicon-o-paper-airplane")
                ->color("warning")
                ->visible(fn (): bool => $this->record->telegramStatus() === "Ошибка")
                ->action(function (): void {
                    $this->record->forceFill([
                        "telegram_attempts" => 0,
                        "telegram_error" => null,
                    ])->save();

                    Notification::make()
                        ->title("Анонс уйдёт в канал в течение минуты")
                        ->success()
                        ->send();
                }),

            DeleteAction::make(),
        ];
    }
}
