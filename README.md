# Andishkadeh CMS

Laravel 13 CMS with a React/Inertia SSR public website, Filament 5 administration, and PostgreSQL production target.

## Current features

- Admin-managed homepage banners with scheduling and ordering
- Unified content management for posts, courses, books, and announcements
- Series with ordered posts and standalone posts
- Multimedia block editor for text, headings, images, galleries, audio, video, links, quotes, and files
- Draft, scheduled, published, and archived content
- Responsive Persian RTL public homepage, series pages, and content detail pages
- Configurable About, institute-registration, Contact, and Donation sections
- Donation transaction storage prepared for the gateway adapter (gateway selection pending)
- Persian Jalali calendar pickers and table display in the admin panel (via `mokhosh/filament-jalali`), with Gregorian timestamps retained in the database
- Server-side rendering for public pages

See [the active blueprint](doc/blueprint.md) and [decision log](doc/need-to-know.md) before adding another feature.

## Local setup

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan storage:link
php artisan make:filament-user
```

The default local `.env` uses SQLite for quick setup. Set `DB_CONNECTION=pgsql` and the PostgreSQL connection variables for the production-shaped environment described in the blueprint. Uploaded media is stored on Laravel's public disk; the `storage:link` command is required.

Use `composer dev` for normal local development. The public site is available at `/` and the admin panel at `/admin`.

The demo seed creates `test@example.com` with password `password` for local review only. Replace it with a real administrator before deployment.

## Verification

```bash
composer test
npm run check
npm run build
vendor/bin/pint --test
```

The production environment must run the Laravel application, PostgreSQL, queue workers, the scheduler, and the Inertia SSR process:

```bash
php artisan inertia:start-ssr
```
