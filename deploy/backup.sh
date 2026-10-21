#!/usr/bin/env bash
# Бэкап базы в ~/backups/meister на сервере. Каждую ночь в 03:45 его запускает cron (deploy/install-cron.sh)
# — на 15 минут позже Битулята, чтобы не грузить сервер одновременно, — а ещё update.sh перед каждым
# обновлением. Хранятся последние 14 дней. Картинки статей лежат в Object Storage Яндекса, их тут нет.
#   ./deploy/backup.sh            — обычный бэкап
#   ./deploy/backup.sh метка      — с меткой в имени (before-update и т. п.)
# Восстановить: ./deploy/restore.sh ~/backups/meister/файл.sql.gz
set -euo pipefail
cd "$(dirname "$0")/.."

DIR="$HOME/backups/meister"
LABEL=${1:-daily}
FILE="$DIR/meister-$(date +%F-%H%M)-$LABEL.sql.gz"
mkdir -p "$DIR"
chmod 700 "$DIR"

# --single-transaction — снимок без блокировки таблиц: сайт работает во время бэкапа.
# < /dev/null: ввод не нужен, а без этого docker забирает чужой stdin (скрипт, переданный следом по ssh)
./deploy/dc exec -T db sh -c 'MYSQL_PWD="$MYSQL_ROOT_PASSWORD" mysqldump -u root --single-transaction --routines --no-tablespaces "$MYSQL_DATABASE"' \
    < /dev/null | gzip > "$FILE.tmp"
mv "$FILE.tmp" "$FILE"
chmod 600 "$FILE"

find "$DIR" -name 'meister-*.sql.gz' -mtime +14 -delete
echo "Бэкап: $FILE ($(du -h "$FILE" | cut -f1))"
