#!/bin/bash

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
