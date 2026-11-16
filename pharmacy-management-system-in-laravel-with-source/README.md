# PharmaCare: Pharmacy Management System in Laravel 13

A complete pharmacy system that **watches every expiry date for you**: an expiry dashboard, a barcode sell screen that refuses expired medicine, a transaction-safe checkout with a row lock, receipts, sales history and tests.

- Laravel 13 (PHP 8.3+), MySQL / MariaDB, Blade views, plain CSS (no npm needed)
- Demo data only: fake medicines and one demo account

## Features
- **Dashboard**: expired, expiring within 60 days, and low stock, problems first
- **Medicines**: search by name, generic name or barcode; add, edit, delete; unique barcodes
- **Sell screen**: scan (type the barcode + Enter), cart, cash and change; expired and out-of-stock items are blocked
- **Checkout**: one database transaction, stock reduced with `lockForUpdate()` so two cashiers cannot sell the same last box
- **Receipts and sales history**
- **7 feature tests** (`php artisan test`)

## Run it (5 commands)
1. Start MySQL (XAMPP, Laragon or any MySQL/MariaDB) and create an empty database named `pharmacare`.
2. In this folder:

```
composer install
copy .env.example .env        (Mac/Linux: cp .env.example .env)
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

3. Open http://localhost:8000 and log in with **demo@example.com** / **Demo@1234**.

The database settings are in `.env` (`DB_DATABASE=pharmacare`, `DB_USERNAME=root`, empty `DB_PASSWORD` for a default XAMPP). Change them if your MySQL is different.

## Demo barcodes (type them on the Sell screen)
| Barcode | Medicine | Note |
|---|---|---|
| 480000001000 | Biogesic 500mg | fine |
| 480000001037 | Advil 200mg | fine |
| 480000001222 | Vitamin C 500mg | fine |
| 480000001333 | Imodium 2mg | **expired**, blocked |
| 480000001518 | Mefenamic 500mg | **expired**, blocked |

The seeder sets the expiry dates relative to today, so there are always 2 expired, 3 expiring soon and 3 low-stock medicines.

## Tests
```
php artisan test
```
The tests use an in-memory SQLite database (see `phpunit.xml`), so they never touch your MySQL data. Do not export `DB_CONNECTION` or other `DB_*` environment variables in the shell where you run the tests: real environment variables override `phpunit.xml`.

## Where to look
| File | What it does |
|---|---|
| `database/migrations/2026_11_15_000001_create_pharmacy_tables.php` | the four tables |
| `app/Models/Medicine.php` | the expiry scopes: `expired`, `expiringSoon`, `lowStock`, and the status badge |
| `app/Http/Controllers/DashboardController.php` | the dashboard queries |
| `app/Http/Controllers/PosController.php` | barcode lookup (blocks expired) and the checkout transaction |
| `resources/views/pos/index.blade.php` | the sell screen script |
| `tests/Feature/PharmacyTest.php` | the 7 tests |

## Honest limits
A learning and capstone project: demo data only, the barcode scanner is simulated by typing (a USB scanner types the same way), there is no SMS yet, and it is not meant for real clinical use without more checks and audits.
