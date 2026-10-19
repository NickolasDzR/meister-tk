# План выкладки meister-tk (сайт со статьями, без CRM)

Составлен 2026-10-19 в сессии Битулята, пока ничего не сделано. Шаги 1–4 — код и проверка локально,
5–8 — сервер. Каждый шаг на сервере — только по слову Николая.

## Где будет жить

Тот же VPS, что и Битулят: Рег.облако, 2 ядра, 2 ГБ, 40 ГБ; `ssh bitulyat` из WSL, пользователь deploy.
На 2026-10-19 занято ~660 МБ памяти из 2 ГБ, 7 ГБ диска, нагрузка около нуля — сервер мощнее не нужен.
Мейстер добавит ~60–100 МБ (PHP) и 300–450 МБ (MySQL). Пересмотреть, когда появится CRM.

Что уже есть на сервере (репозиторий bitulyat-proto, папка `~/bitulyat`):
- `compose.prod.yaml`: web (Caddy, единственный слушает 80/443), app (php-fpm), db (Postgres).
- `deploy/Caddyfile` — HTTPS сам (Let's Encrypt), HTTP/3 выключен.
- `deploy/update.sh`: бэкап → build → up → migrate → проверка `/up`. `make deploy` = check + `git push server main` + update.sh.
- cron: бэкап 03:30, демо 04:00. Бэкапы в `~/backups`.

Эту схему повторить — она уже проверена. Скрипты брать из bitulyat-proto/deploy и править под MySQL.

## Как соединить два сайта

Порты 80/443 заняты Caddy Битулята, второй Caddy наружу не поднять. Схема:

```
интернет → Caddy Битулята (80/443) ─┬─ bitulyat.ru  → app Битулята
                                    └─ домен Мейстера → reverse_proxy meister-web:80
                                                        (веб-контейнер Мейстера без портов наружу)
                                                        → php-fpm Мейстера → MySQL Мейстера
```

- Общая docker-сеть `edge` (external, один раз: `docker network create edge`).
- В Битуляте: web подключить к `edge`, в Caddyfile добавить блок домена Мейстера (и www → редирект).
  Это правка в репозитории bitulyat-proto и выкатка Битулята — согласовать отдельно, после неё проверить bitulyat.ru.
- Мейстер: свой веб-контейнер (Caddy или nginx, без `ports:`) в сетях `default` и `edge`, алиас `meister-web`.
- На потом: вынести Caddy в отдельный стек `~/edge`, чтобы сайты не зависели друг от друга. Сейчас не нужно.

## Шаги

### 1. Боевой Docker (в репозитории meister-tk)
- `docker/Dockerfile.prod`: php 8.3-fpm **без xdebug**, opcache включён, `composer install --no-dev --optimize-autoloader`,
  код копируется в образ (не volume). Фронт — отдельной стадией node: `npm ci && npm run build` (вместе со скриптом
  `images`, sharp → webp), в итоговый образ только `public/build`.
- Зеркало apt: в Рег.облаке не открывается deb.debian.org — зеркало Яндекса, как в Dockerfile.prod Битулята.
- Старт контейнера: `config:cache`, `route:cache`, `view:cache`, `filament:optimize`, `storage:link`.
- `compose.prod.yaml`: web (без портов, сеть edge), app, db (mysql:8.0, `utf8mb4` / `utf8mb4_unicode_ci`, а не utf8,
  как сейчас в dev), тома db_data и storage. **Без phpMyAdmin**, 3306 наружу не пробрасывать. `restart: unless-stopped`.
- MySQL под 2 ГБ: `innodb_buffer_pool_size=128M`, `performance_schema=OFF`, `max_connections=30`.
- `deploy/dc`, `deploy/update.sh`, `deploy/backup.sh`, `deploy/init-env.sh`, `make deploy` — по образцу Битулята.
- Сборка на сервере даёт пик памяти 1–1,5 ГБ. Сначала попробовать как есть и смотреть `free -h`; если тесно — собирать
  образ локально.

### 2. Окружение `.env.production` (только на сервере, не в git)
`APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://<домен>`, `APP_LOCALE=ru`, новый `APP_KEY`,
`DB_CONNECTION=mysql`, `DB_HOST=db`, сильный пароль базы, `SESSION_SECURE_COOKIE=true`, `LOG_LEVEL=warning`.

Ключи сайта — спросить у Николая, где лежат; в код и в чат не вставлять:
- `DADATA_API_KEY`, `DADATA_SECRET_KEY` — подсказки городов (`/address-suggest`).
- `YMAP_API_KEY` — карта. В кабинете Яндекса ограничить ключ доменом.
- Диск `yandex` (Object Storage) — картинки статей из админки: ключи, бакет, endpoint.
- Почта — если какая-то форма что-то отправляет (проверить; сейчас `MAIL_MAILER=log`).

### 3. Безопасность и мелочи
- `/styleguide` — проверить, что в проде закрыт.
- `chmod -R 777` (`make app-grant-write-permission`) — только dev; в проде права через владельца www-data.
- Filament: `canAccessPanel` пускает только своих; первого админа создать на сервере `make:filament-user`.
- `TrustProxies` доверяет Caddy — иначе у счётчика просмотров все посетители с одного IP.
- `robots.txt`, `sitemap.xml`, favicon, страницы 404/500 в стиле сайта.

### 4. Проверка локально
- Поднять `compose.prod.yaml` у себя: главная, список статей, статья, админка, загрузка картинки, подсказки адреса, карта.
- `composer test`, `npm run build` без ошибок, Lighthouse главной и статьи, телефон 360 и 320 px.

### 5. Домен и DNS
- A-записи домена и www на IP сервера. Дождаться, пока резолвится (`dig +short`), и только потом включать блок в Caddy —
  иначе Let's Encrypt не выдаст сертификат и может временно заблокировать попытки.

### 6. Первая выкладка
- `~/meister-tk` на сервере + приёмный git-репозиторий для `git push server main` (как у Битулята), `.env.production`.
- `docker network create edge`, подключить Битулят, `./deploy/dc up -d`, `migrate --force`, сидеры только нужные для прода,
  создать админа.
- Статьи, наполненные локально, — один раз `mysqldump` локальной базы на сервер (база прода ещё пустая).
  После этого главная база — на сервере, локальную туда больше не лить.

### 7. Бэкапы
- `deploy/backup.sh`: `mysqldump --single-transaction` в `~/backups/meister`, хранить 14 дней.
- cron в 03:45 (Битулят в 03:30 — не одновременно). Картинки лежат в Object Storage.
- Один раз руками проверить восстановление.

### 8. После выкладки
- Проверить с телефона без VPN (с VPN сайты на этом сервере не открываются — известная проблема Рег.облака).
- Яндекс.Вебмастер: подтвердить сайт, отправить sitemap. Метрика — если нужна.
- Ошибки в Telegram, как в Битуляте (`App\Support\ErrorAlert`), можно тем же ботом.

## Риски
- Один сервер на два сайта: упал — легли оба. Для статей приемлемо.
- Правка Caddy Битулята затрагивает его выкладку — проверить bitulyat.ru сразу после.
- Сборка во время выкладки может притормозить Битулят — выкладывать утром.
