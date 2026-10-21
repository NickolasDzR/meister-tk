#!/usr/bin/env bash
# Выкатить новую версию на сервере. Код к этому моменту уже пришёл через git push (make deploy).
#   1. Бэкап базы перед обновлением — если миграция что-то сломает, есть к чему вернуться
#   2. Сборка образов и перезапуск контейнеров (сайт недоступен секунды, пока app перезапускается)
#   3. Миграции базы
#   4. Проверка, что сайт отвечает
set -euo pipefail
cd "$(dirname "$0")/.."

DC=./deploy/dc

if $DC ps --status running db 2>/dev/null | grep -q db; then
    ./deploy/backup.sh before-update
fi

$DC build --pull
$DC up -d --remove-orphans
$DC exec -T app php artisan migrate --force < /dev/null

# Старые образы после пересборки — мусор, занимают диск
docker image prune -f >/dev/null

# /up — встроенная проверка Laravel. Сначала изнутри (наш Caddy → PHP → база),
# потом снаружи через Caddy Битулята по https — так видно, на каком звене сломалось
for attempt in $(seq 1 30); do
    if $DC exec -T web wget -q -O /dev/null http://127.0.0.1/up < /dev/null 2>/dev/null; then
        if curl -fsSk -o /dev/null --max-time 5 --resolve tk-meister.ru:443:127.0.0.1 https://tk-meister.ru/up; then
            echo '==> Готово: сайт отвечает по https://tk-meister.ru'
        else
            echo '==> Мейстер отвечает внутри, но не через Caddy Битулята: проверь блок tk-meister.ru в его Caddyfile и сеть edge'
        fi
        exit 0
    fi
    sleep 2
done
echo '==> Контейнеры запущены, но сайт не ответил за минуту. Смотри: ./deploy/dc logs --tail=100'
exit 1
