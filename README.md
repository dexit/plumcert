# Plumcert (PHP / Laravel)

Gas engineer field service & admin platform.

## Stack

- PHP 8.3+
- Laravel 13.8
- Filament 4 (admin panel — Livewire/Alpine, no Vue)
- SQLite default (MySQL switchable)
- dompdf — PDF certificates
- Sanctum — API tokens for Field Service Companion mobile app
- Horizon — queue management
- Telescope — debug panel
- Spatie webhook-client + webhook-server

## Quick start

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

Then visit:
- `/` — public website
- `/admin` — Filament admin (default: `admin@plumcert.local` / `password`)
- `/horizon` — queue dashboard (admin only)
- `/telescope` — debug panel (admin only)
- `/api/v1/*` — mobile API (Sanctum auth)

## Switch to MySQL

Edit `.env`:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=plumcert
DB_USERNAME=root
DB_PASSWORD=
```

## Architecture

| Layer | Tech |
|---|---|
| Public site | Blade + Tailwind CDN |
| Admin panel | Filament 4 (Livewire) |
| Mobile API | Sanctum REST `/api/v1/` |
| PDFs | dompdf renders Blade views in `resources/views/certificates/` |
| Queues | Horizon (Redis recommended; falls back to database) |
| Webhooks | Spatie webhook-client (Stripe) + webhook-server (outbound) |

## Models

companies, users, customers, properties, boilers, jobs, leads, certificates, findings, quotes, invoices, reminders

## Certificates (9 PDF types)

CP12 Homeowner, CP12 Landlord, Gas Warning Notice, Installation/Commissioning Checklist, Gas Service Record, Minor Works, Disconnection Notice, Invoice, Quote.

## Scheduler

- Daily 08:00: `reminders:generate` — scans boilers with service due ≤ 30 days
- Hourly: `reminders:process` — sends pending reminders via email

Add cron: `* * * * * cd /path-to-plumcert && php artisan schedule:run >> /dev/null 2>&1`

## Tools (public calculators)

`/tools` — Gas Rate, Pipe Sizing, Heat Loss, Installation Volume (client-side JS).

## API endpoints

~60 endpoints under `/api/v1/`. See `routes/api.php`.

## Tests

```bash
php artisan test
```

## License

MIT
