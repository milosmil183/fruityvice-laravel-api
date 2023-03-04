## Fruityvice API Backend

## Getting Started

Composer install
```bash
composer install
```

Setup env file
```bash
cp .env.example .env
```

Create database
```bash
touch database/database.sqlite
```

Database migration
```bash
php artisan migrate
# or
php artisan migrate:refresh
```

Run local development server
```bash
php artisan serve
```

Artisan command to fetch all fruits & update database
```bash
php artisan fetch-fruits
# you can specify `--notify` if you want to send email
# email driver is not configured yet, so mailing is not done
php artisan fetch-fruits --notify
```

Boom! All done!
The local dev server will be running at:
http://localhost:8000
