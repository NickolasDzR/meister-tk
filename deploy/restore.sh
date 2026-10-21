#!/usr/bin/env bash
# Восстановить базу из бэкапа: ./deploy/restore.sh ~/backups/meister/файл.sql.gz
# Текущее содержимое базы заменяется целиком — перед этим делается свежий бэкап с меткой before-restore.
set -euo pipefail
cd "$(dirname "$0")/.."

FILE=${1:?Укажи файл бэкапа: ./deploy/restore.sh ~/backups/meister/файл.sql.gz}
[[ -f "$FILE" ]] || { echo "Нет файла $FILE"; exit 1; }

./deploy/backup.sh before-restore
gunzip -c "$FILE" | ./deploy/dc exec -T db sh -c 'MYSQL_PWD="$MYSQL_ROOT_PASSWORD" mysql -u root "$MYSQL_DATABASE"'
./deploy/dc exec -T app php artisan optimize:clear < /dev/null
./deploy/dc restart app
echo "Восстановлено из $FILE"
