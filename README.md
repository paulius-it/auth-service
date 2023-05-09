# Shipping authentication service

## How to launch
- PHP8.1 and Composer should be installed;
- Docker and cocker compose should be available in order to launch it in the container.
In the terminal run:
```bash
cp .env.example .env
composer install
vendor/bin/sail artisan key:generate
vendor/bin/sail up -d
```

Authentication service is ready - it should be available at 'localhost:8000'.
