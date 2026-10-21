#!/usr/bin/env bash
# Ставит задачи в cron пользователя deploy:
#   — ночной бэкап базы каждый день в 03:45;
#   — планировщик Laravel раз в минуту (анонсы статей в Telegram).
# Повторный запуск не дублирует строки. Посмотреть: crontab -l
set -euo pipefail
DIR="$(cd "$(dirname "$0")/.." && pwd)"
BACKUP="45 3 * * * $DIR/deploy/backup.sh >> $HOME/backups/cron.log 2>&1"
SCHEDULE="* * * * * $DIR/deploy/dc exec -T app php artisan schedule:run >> /dev/null 2>&1"
( crontab -l 2>/dev/null | grep -vF "$DIR/deploy/backup.sh" | grep -vF "schedule:run" ; echo "$BACKUP" ; echo "$SCHEDULE" ) | crontab -
echo "cron: $BACKUP"
echo "cron: $SCHEDULE"
