#!/bin/bash

set -e

PORT="${PORT:-10000}"

sed -i "s/Listen 80/Listen ${PORT}/g" /etc/apache2/ports.conf
sed -i "s!<VirtualHost \*:80>!<VirtualHost *:${PORT}>!" /etc/apache2/sites-available/000-default.conf

echo "🚀 Starting Laravel application..."

# Cache config & routes
echo "📦 Caching configuration and routes..."
php artisan config:cache || true
php artisan route:cache || true

# Run migrations (chỉ chạy lần đầu hoặc khi cần)
echo "🗄️ Running migrations..."
php artisan migrate --force || true

# Start Apache
echo "✅ Starting Apache..."
apache2-foreground
