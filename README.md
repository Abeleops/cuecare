# GG Billiards & Autocare POS

CueCare is a web-based point-of-sale and inventory application for GG Billiards & Autocare, built with Laravel, MySQL, Blade, and Vite.

## Features

- Dashboard with sales totals, top-selling products, and sales by category
- Point of sale with a persistent cart and cash, GCash, or split payments
- Product and category management, stock adjustments, and inventory tracking
- Customer and staff management with role-based access
- Sales history, printable receipts, and Excel report exports

GCash payments are recorded by the POS; the application does not process payments through a GCash gateway. Split cash and GCash amounts must add up to the sale total.

## Roles

Administrators manage the application, cashiers can use the POS and view their own sales, and staff can view products and adjust inventory. Access depends on the user's assigned role and the permission configuration.

## Requirements

- PHP 8.3 or newer, with extensions required by Composer and Excel exports
- Composer
- Node.js and npm compatible with the versions in `package.json`
- MySQL or MariaDB

## Local setup

Run these commands from the project directory. On Windows with Herd, use its PHP executable if `php` is unavailable in your terminal.

1. Create a MySQL database named `cuecare_db` using phpMyAdmin or your database tool.
2. Create a `.env` file in the project root if one does not already exist. This repository does not currently include an `.env.example`. Start with these settings and replace the database credentials:

   ```dotenv
   APP_NAME="GG Billiards & Autocare POS"
   APP_ENV=local
   APP_KEY=
   APP_DEBUG=true
   APP_URL=http://127.0.0.1:8000

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=cuecare_db
   DB_USERNAME=root
   DB_PASSWORD=

   SESSION_DRIVER=database
   CACHE_STORE=database
   QUEUE_CONNECTION=sync
   ```

3. Install dependencies:

   ```bash
   composer install
   npm install
   ```

4. For a new installation with an empty `APP_KEY`, generate the application key:

   ```bash
   php artisan key:generate
   ```

   Keep the key for an existing installation. Changing it can invalidate sessions and make encrypted data unreadable.

5. Create the database tables and build the assets:

   ```bash
   php artisan migrate
   npm run build
   php artisan storage:link
   ```

   The storage link makes public uploads accessible. Windows may require permission to create symbolic links.

6. For a local demo, optionally seed roles, categories, an administrator, and sample data:

   ```bash
   php artisan db:seed
   ```

   The local administrator seeder creates or resets the `test` account with password `test1234`. It runs only in local or testing environments. These credentials are for development only. Seeding also modifies existing records, so review the seeders before running them against data you need to preserve.

7. Start the application:

   ```bash
   php artisan serve
   ```

   Open `http://127.0.0.1:8000`. For frontend development, run `npm run dev` in another terminal instead of relying on the production build.

## Reports

Sales and inventory reports can be exported as formatted Excel (`.xlsx`) files. Sales reports accept a date range of up to 30 days. Split payments appear in unfiltered results but are excluded from the cash-only and GCash-only filters.

## Tests

Create a separate, disposable MySQL database named `cuecare_test` before running the suite. `phpunit.xml` forces the MySQL connection and this database name; it uses the configured host and credentials. Tests create and change data, so never point them at a database containing important records.

```bash
php artisan test
```

To inspect routes and middleware:

```bash
php artisan route:list
```

## Deployment

Use a PHP host with compatible extensions and MySQL or MariaDB. Configure a separate production database and application key, set `APP_ENV=production` and `APP_DEBUG=false`, use HTTPS, build the Vite assets, and point the web server's document root to Laravel's `public` directory. Keep `.env` and the rest of the project outside the public web root. Configure backups and verify uploads, receipts, and Excel exports before using real business data.

InfinityFree may be useful for a demonstration, but its shared-hosting restrictions make deployment more involved. Review its [Laravel installation guide](https://forum.infinityfree.com/t/how-to-install-a-laravel-site-on-infinityfree/118578), verify the available PHP version and extensions, and test database access and file uploads before relying on it for the POS.

## Troubleshooting

- **Vite manifest missing or CSS/JavaScript not loading:** Run `npm install` and `npm run build`, or start `npm run dev` and keep it running while developing. Check that the build finishes successfully.
- **Database connection or migration errors:** Check the database name, credentials, and server in `.env`. Review existing data and schema before rerunning migrations.
- **Images not appearing:** Check that `php artisan storage:link` completed and that the storage directories are writable.
- **Configuration changes not taking effect:** Run `php artisan config:clear` and retry.
