
# Digital Marketplace

A Laravel-based marketplace application maintained in this repository. The project uses Laravel 9, modular structure (`Modules/`), and integrates multiple payment gateways and storefront features.

## Quick overview

- **Framework:** Laravel 9
- **Language:** PHP >= 8.0.2
- **Frontend:** Vite, React (optional), Bootstrap, Sass
- **Modules:** `nwidart/laravel-modules` (code in `Modules/`)
- **Payments:** Stripe, PayPal, Razorpay, MercadoPago, Mollie, Iyzico, PayMongo (see `composer.json`)

## Key features

- Modular architecture with `Modules/` for domain separation
- Shopping cart support (`bumbummen99/shoppingcart`)
- Multiple payment gateway integrations
- Image handling (`intervention/image`) and PDF generation (`barryvdh/laravel-dompdf`)
- JWT auth support (`tymon/jwt-auth`) and social login

## Requirements

- PHP >= 8.0.2
- Composer
- Node.js (v16+) and npm / Yarn
- MySQL (or other DB supported by Laravel)
- Optional: Redis, AWS S3 (for files), Pusher (for realtime)

## Setup (development)

1. Clone the repository

	git clone <repo-url>
	cd Digital_marketplace

2. Install PHP dependencies

	composer install --no-interaction --prefer-dist

3. Copy environment file and generate app key

	copy .env.example .env
	php artisan key:generate

4. Configure `.env` (database, mail, payment credentials)

	- DB_CONNECTION, DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD
	- MAIL_* settings
	- Payment gateway keys (Stripe, PayPal, RAZORPAY_*, MERCADOPAGO_*, etc.)
	- `JWT_SECRET` (exists in `.env.example`)

5. Install JS dependencies and build assets

	npm install
	npm run dev

6. Run migrations and optional seeders

	php artisan migrate
	php artisan db:seed

7. Create storage symlink

	php artisan storage:link

8. Serve the application

	php artisan serve

Or configure your webserver (Apache/Nginx) to point to the `public/` directory.

## Environment variables

This repo includes an example `.env.example`. Important variables to set:

- `APP_URL`, `APP_ENV`, `APP_DEBUG`
- Database: `DB_*`
- Mail: `MAIL_*`
- Filesystem/AWS: `AWS_*`
- Realtime: `PUSHER_*`, `VITE_PUSHER_*`
- Payment credentials for the gateways used in `composer.json`
- `JWT_SECRET`, `APP_MODE`

## Running tests

Run unit and feature tests with PHPUnit or Artisan:

	./vendor/bin/phpunit
	# or
	php artisan test

## Useful Artisan commands

- `php artisan migrate` — run migrations
- `php artisan db:seed` — run seeders
- `php artisan queue:work` — process queued jobs
- `php artisan route:list` — view registered routes

## Project structure (high level)

- `app/` — application core (models, controllers, providers)
- `Modules/` — modular features and domain code
- `resources/` — views, frontend assets
- `public/` — web entrypoint and compiled assets
- `routes/` — route definitions (`web.php`, `api.php`)

## Notes & tips

- Payments: check `config` files and `.env` for gateway credentials before testing payments.
- The project uses `nwidart/laravel-modules`; to work with modules, see `Modules/` and the package docs.
- If you get permission errors with storage, ensure `storage/` and `bootstrap/cache` are writable.

## Contributing

If you'd like to contribute, please open issues or pull requests. Follow standard Laravel contribution practices and ensure tests pass.

## License

This project uses the MIT license (see `composer.json`).

---

If you want, I can update the README with more details (setup for Docker, CI, exact seeders, environment checklist, or example `.env` values). Tell me which you'd like next.
