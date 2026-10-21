#!/usr/bin/env bash
# Ставит ночной бэкап базы в cron пользователя deploy: каждый день в 03:45.
# Повторный запуск не дублирует строку. Посмотреть: crontab -l
set -euo pipefail
DIR="$(cd "$(dirname "$0")/.." && pwd)"
LINE="45 3 * * * $DIR/deploy/backup.sh >> $HOME/backups/cron.log 2>&1"
( crontab -l 2>/dev/null | grep -vF "$DIR/deploy/backup.sh" ; echo "$LINE" ) | crontab -
echo "cron: $LINE"
