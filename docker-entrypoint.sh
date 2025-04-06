#!/bin/sh
set -e

# Aguarda o MySQL ficar disponível
echo "Waiting for MySQL to be ready..."
until mysql -h db -u laravel -psecret -e "SELECT 1" >/dev/null 2>&1; do
  echo "MySQL is unavailable - sleeping"
  sleep 1
done

echo "MySQL is up - executing migrations"

# Executa as migrações do Laravel
php artisan migrate --force

# Executa o comando original (php-fpm)
exec "$@" 