#!/bin/bash

# Sales Dashboard Backend Deployment Script
# This script handles the deployment of the Laravel backend

echo "========================================="
echo "Sales Dashboard Backend Deployment"
echo "========================================="

# Check if .env file exists
if [ ! -f .env ]; then
    echo "Error: .env file not found!"
    echo "Please copy env.example to .env and configure it."
    exit 1
fi

# Install/update dependencies
echo "Installing Composer dependencies..."
composer install --optimize-autoloader --no-dev

# Generate application key if not set
if ! grep -q "APP_KEY=base64:" .env; then
    echo "Generating application key..."
    php artisan key:generate
fi

# Clear and cache config
echo "Optimizing application..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Cache configurations
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations
echo "Running database migrations..."
read -p "Do you want to run migrations? (y/n) " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    php artisan migrate --force
fi

# Create storage link
echo "Creating storage symlink..."
php artisan storage:link

# Set permissions
echo "Setting permissions..."
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true

echo ""
echo "========================================="
echo "Deployment completed successfully!"
echo "========================================="
echo ""
echo "Post-deployment checklist:"
echo "1. Verify .env configuration"
echo "2. Check file permissions"
echo "3. Test API endpoints"
echo "4. Monitor error logs"

