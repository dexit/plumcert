# CLAUDE.md

This file provides guidance to Claude Code when working with code in this repository.

## Commands

```bash
# Install PHP dependencies
composer install

# Install JS dependencies (Vite/Tailwind assets)
npm install

# Start the dev server
php artisan serve

# Run database migrations
php artisan migrate

# Seed with demo data
php artisan db:seed

# Run the full test suite
php artisan test

# Syntax-check without running
php -l app/**/*.php

# Queue worker (for emails/reminders)
php artisan horizon

# Build frontend assets
npm run build
```

## Environment Variables

| Variable | Description |
|---|---|
| `APP_KEY` | Laravel app key (generate with `php artisan key:generate`) |
| `DB_CONNECTION` | `sqlite` (default) or `mysql` |
| `MAIL_MAILER` | `smtp` / `log` / `array` |
| `SMS_DRIVER` | `twilio` or `log` (default) |
| `TWILIO_SID` | Twilio account SID |
| `TWILIO_TOKEN` | Twilio auth token |
| `TWILIO_FROM` | Twilio SMS number |
| `TWILIO_WHATSAPP_FROM` | Twilio WhatsApp sender |
| `ANTHROPIC_API_KEY` | Claude API key — enables AI quote generation |
| `GOOGLE_CALENDAR_ID` | Google Calendar ID for booking events |

## Architecture

Laravel 13 + Filament 4 admin panel. See `README.md` for the full architecture overview.

### Key directories

- `app/Filament/` — admin panel resources, pages, widgets, actions
- `app/Models/` — Eloquent models
- `app/Services/` — business logic (RecurringJobScheduler, SmsSender, AiAssistant, NominatimLookup, PdfCertificate)
- `app/Console/Commands/` — artisan commands (reminders:generate, reminders:process, reminders:report)
- `database/migrations/` — all schema migrations
- `resources/views/certificates/` — dompdf Blade templates for 9 PDF types
- `resources/views/portal/` — customer self-serve portal
- `routes/api.php` — ~60 Sanctum-auth mobile API endpoints
- `routes/web.php` — public site + portal routes
- `tests/` — PHPUnit feature + unit tests (29 tests)

### Admin panel

Visit `/admin` (default: `admin@plumcert.local` / `password`).

### Mobile API

All endpoints under `/api/v1/` require `Authorization: Bearer <token>` (Sanctum).

### Deployment

SQLite by default. Switch to MySQL/MariaDB via `.env`. Run `php artisan migrate --seed` then `php artisan serve`.
