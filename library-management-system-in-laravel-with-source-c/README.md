# BookNest: Library Management System in Laravel 13

A complete library system where **every book and every member is a QR code**: scan the member card, scan the book, and the loan is recorded with a due date. Late returns get a fine automatically, and a daily command emails members who have an overdue book.

- Laravel 13 (PHP 8.3+), SQLite by default (nothing to install), Blade views, plain CSS (no npm needed)
- QR codes are drawn as SVG with `bacon/bacon-qr-code` (no PHP extension needed)
- Demo data only: fake books, fake members, one demo account

## Features
- **Dashboard**: books, members, on loan, overdue; overdue list with the fine so far; books due in 3 days
- **Counter**: borrow = scan member + scan book; return = scan book. A scanner types the code and presses Enter, so a typed code works too
- **Borrow rules**: one copy per book, max 3 books per member, no new loans while a book is overdue. The check runs in a transaction with `lockForUpdate()`
- **Fines**: 5 per day late, worked out when the book is returned and shown live for overdue loans
- **Books and members**: search, add, edit, delete (blocked while a loan is open)
- **QR labels** for books and a printable **library card** for each member
- **Overdue emails**: `php artisan library:overdue-notices`, scheduled daily at 08:00
- **11 feature tests**

## Run it
```
composer install
copy .env.example .env        (Mac/Linux: cp .env.example .env)
php artisan key:generate
php artisan migrate --seed
php artisan serve
```
`.env.example` already uses SQLite. Create the empty file `database/database.sqlite` first if Laravel does not offer to create it.

Open http://localhost:8000 and log in with **demo@example.com** / **Demo@1234**.

## Try the Counter (demo codes)
| Code | What | Note |
|---|---|---|
| MB-1001 | Ana Cruz | has 2 books out, can borrow 1 more |
| MB-1002 | Ben Reyes | **overdue**, cannot borrow |
| BK-0003 | Laravel from Scratch | on loan, blocked |
| BK-0004 | The Little SQL Book | available |
| BK-0002 | Learning PHP the Easy Way | available |

Try: member `MB-1001`, book `BK-0004` -> borrowed, due in 14 days. Return `BK-0001` (Ben, 6 days late) -> fine 30.
Print QR labels at **Books > QR labels**, and a card at **Members > Card**. Any phone QR scanner app or a USB scanner works: it just types the code.

The seeder sets dates relative to today, so there are always 2 overdue loans.

## Use MySQL instead of SQLite
Create an empty database, then in `.env` set `DB_CONNECTION=mysql`, `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`, and run `php artisan migrate --seed`.

## Overdue emails
`MAIL_MAILER=log` in `.env.example` writes each email into `storage/logs/laravel.log`, so you can see them without a mail server. To send real emails or SMS, set a real mail driver (or call an SMS API inside `SendOverdueNotices`). To run the schedule on a server: `* * * * * php artisan schedule:run`.

## Tests
```
php artisan test
```
They use an in-memory SQLite database (`phpunit.xml`). Do not export `DB_*` variables in the shell where you run the tests: real environment variables override `phpunit.xml`.

## Where to look
| File | What it does |
|---|---|
| `database/migrations/2026_11_18_000001_create_library_tables.php` | members, books, loans |
| `app/Models/Loan.php` | loan rules, `overdue` scope, `daysLate()`, fine |
| `app/Models/Member.php` | `borrow()`: the three rules in one transaction |
| `app/Http/Controllers/CounterController.php` | borrow and return from the Counter |
| `app/Support/Qr.php` | text to QR SVG |
| `app/Console/Commands/SendOverdueNotices.php` | daily overdue emails |
| `tests/Feature/LibraryTest.php` | the 11 tests |
