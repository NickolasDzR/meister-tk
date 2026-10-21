#!/usr/bin/env bash
# Создаёт .env.production на сервере с новыми случайными паролями. Один раз, при первой установке.
# Если файл уже есть — ничего не делает: иначе новый пароль базы не совпадёт со старой базой.
# Пароли в терминал не выводятся; посмотреть их можно только на сервере: cat .env.production
# Ключи сервисов (карты, Dadata, хранилище картинок) сюда не пишутся — их дописывают отдельно.
set -euo pipefail
cd "$(dirname "$0")/.."

if [[ -f .env.production ]]; then
    echo '.env.production уже есть — не трогаю'
    exit 0
fi

DOMAIN=${1:-tk-meister.ru}
umask 077
cat > .env.production <<ENV
APP_NAME="Мейстер"
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:$(openssl rand -base64 32)
APP_URL=https://${DOMAIN}
APP_LOCALE=ru
APP_FALLBACK_LOCALE=en
APP_TIMEZONE=Europe/Moscow

LOG_CHANNEL=daily
LOG_DAILY_DAYS=14
LOG_LEVEL=warning

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=meister
DB_USERNAME=meister
DB_PASSWORD=$(openssl rand -hex 24)
DB_ROOT_PASSWORD=$(openssl rand -hex 24)

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_SECURE_COOKIE=true
CACHE_STORE=database
QUEUE_CONNECTION=sync
FILESYSTEM_DISK=local
MAIL_MAILER=log

# Пускать ли поисковики. Открыть: true + ./deploy/dc restart app
SITE_INDEXING=false
ENV
echo ".env.production создан для ${DOMAIN}"
