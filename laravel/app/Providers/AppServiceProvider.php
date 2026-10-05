<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
            return config('app.frontend_url')."/password-reset/$token?email={$notifiable->getEmailForPasswordReset()}";
        });

        $this->forceHttpsBehindTunnel();
    }

    /**
     * Туннель для отладки на телефоне отдаёт сайт по https, но приложению
     * об этом не сообщает: заголовка X-Forwarded-Proto он не присылает,
     * только адрес. Laravel считает запрос обычным http, ставит ссылки
     * на стили с http:// — и браузер блокирует их на https-странице.
     *
     * Правило только для локальной разработки. Если сайт открыт не по
     * localhost, не по dev.local и не по IP — значит, через туннель,
     * а туннели всегда https.
     */
    private function forceHttpsBehindTunnel(): void
    {
        if (! $this->app->isLocal() || $this->app->runningInConsole()) {
            return;
        }

        $host = request()->getHost();
        $isLocalHost = in_array($host, ['localhost', 'dev.local'], true)
            || filter_var($host, FILTER_VALIDATE_IP) !== false;

        if (! $isLocalHost) {
            URL::forceScheme('https');
        }
    }
}
