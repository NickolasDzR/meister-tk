#!/bin/sh
# Запуск app на сервере: кэш настроек, маршрутов, шаблонов и компонентов админки, потом PHP-FPM.
# Кэш собирается при каждом старте контейнера, поэтому смена .env.production + перезапуск — всегда в силе.
set -e
php artisan optimize
php artisan filament:optimize
exec php-fpm
