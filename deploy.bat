@echo off
REM Sales Dashboard Backend Deployment Script (Windows)
REM This script handles the deployment of the Laravel backend

echo =========================================
echo Sales Dashboard Backend Deployment
echo =========================================

REM Check if .env file exists
if not exist .env (
    echo Error: .env file not found!
    echo Please copy env.example to .env and configure it.
    exit /b 1
)

REM Install/update dependencies
echo Installing Composer dependencies...
call composer install --optimize-autoloader --no-dev

REM Generate application key if not set
findstr /C:"APP_KEY=base64:" .env >nul
if errorlevel 1 (
    echo Generating application key...
    php artisan key:generate
)

REM Clear and cache config
echo Optimizing application...
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

REM Cache configurations
php artisan config:cache
php artisan route:cache
php artisan view:cache

REM Run migrations
echo Running database migrations...
set /p MIGRATE="Do you want to run migrations? (y/n) "
if /i "%MIGRATE%"=="y" (
    php artisan migrate --force
)

REM Create storage link
echo Creating storage symlink...
php artisan storage:link

echo.
echo =========================================
echo Deployment completed successfully!
echo =========================================
echo.
echo Post-deployment checklist:
echo 1. Verify .env configuration
echo 2. Check file permissions
echo 3. Test API endpoints
echo 4. Monitor error logs

pause

