# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Full-stack web application for a Russian logistics/cargo transport company ("Мейстер"). Built on Laravel 13 (PHP 8.3) with a React 19 + TypeScript frontend, fully containerized via Docker.

## Commands

### Docker-based development (primary)

```bash
make init          # Full initialization: start containers, install deps, migrate, seed
make up            # Start all containers
make stop          # Stop containers
make bash          # Shell into Laravel container
make laravel_logs  # Tail Laravel logs
```

### Frontend

```bash
npm run dev        # Vite dev server with image/font processing
npm run build      # Production build
```

### Laravel (inside container or with `make bash`)

```bash
composer dev       # Start all dev processes concurrently (php serve, queue, vite, logs)
composer test      # Run PHPUnit tests
php artisan migrate
php artisan db:seed
```

## Architecture

### Stack

- **Backend:** Laravel 13, PHP 8.3-FPM, MySQL 8.0
- **Frontend:** React 19, TypeScript, Vite, SCSS, Tailwind CSS 4, Bootstrap 5
- **Infrastructure:** Docker Compose — Nginx (ports 8083/8084), PHP-FPM, MySQL, phpMyAdmin (port 8086)

### Frontend structure (`laravel/resources/`)

Component-based architecture with co-located assets:

- `views/layouts/` — master Blade layout (`app.blade.php`)
- `views/components/` — reusable UI primitives (button, input, select, hamburger, preloader, etc.)
- `views/modules/` — page sections (header, footer, nav, main-slider, post-cards, forms/cargo-calc, etc.)
- `ts/` — TypeScript entry (`app.ts`), `modules/`, `helpers/`
- `scss/` — global styles in `base/` and shared utilities in `utils/` (`_variables`, `_grid`, `_vendor`)

Each module can have co-located `.scss` and `.ts` files alongside its `.blade.php` template.

### Vite path aliases (`vite.config.js`)

| Alias | Resolves to |
|-------|-------------|
| `@components` | `resources/views/components` |
| `@modules` | `resources/views/modules` |
| `@` / `@utils` | `resources/ts/helpers` |
| `@types` | `resources/ts/helpers/types` |
| `$` / `@scss` | `resources/scss/utils` |

### Routes

Currently minimal — defined in `laravel/routes/web.php`:
- `GET /` → home view
- `GET /posts` → posts list view

### Database

Migrations in `laravel/database/migrations/`. Currently: users, cache, jobs tables. Seeders in `database/seeders/`.
