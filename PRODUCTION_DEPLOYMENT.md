# Production Deployment from GitHub

## ✅ Backend Successfully Pushed!

**GitHub Repository:** https://github.com/bendamz30/kpiapi.cclpi.com.ph.git

---

## 🚀 Deploy to Production (kpiapi.cclpi.com.ph)

### Initial Setup (First Time Only)

SSH into your production server:

```bash
# Clone the repository
cd /var/www
git clone https://github.com/bendamz30/kpiapi.cclpi.com.ph.git backend
cd backend

# Copy and configure environment
cp env.example .env

# Edit .env with production settings
nano .env

# Required configuration:
# APP_URL=https://kpiapi.cclpi.com.ph
# FRONTEND_URL=https://kpi.cclpi.com.ph
# SANCTUM_STATEFUL_DOMAINS=kpi.cclpi.com.ph
# SESSION_DOMAIN=.cclpi.com.ph
# DB_DATABASE=your_database
# DB_USERNAME=your_username
# DB_PASSWORD=your_password

# Install dependencies
composer install --optimize-autoloader --no-dev

# Generate application key
php artisan key:generate

# Run migrations
php artisan migrate --force

# Create storage symlink
php artisan storage:link

# Set permissions
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 🔄 Update Deployment (When You Push Changes)

### Step 1: Push from Local Machine (Windows)

```bash
cd C:\xampp\htdocs\salesdashboard\backend
git add .
git commit -m "Your change description"
git push origin main
```

### Step 2: Deploy on Production Server

```bash
cd /var/www/backend

# Pull latest changes
git pull origin main

# Update dependencies
composer install --optimize-autoloader --no-dev

# Run any new migrations
php artisan migrate --force

# Clear caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Re-cache for performance
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Check logs
tail -f storage/logs/laravel.log
```

---

## ⚡ Quick Update Script

Create `/var/www/backend/update.sh`:

```bash
#!/bin/bash
echo "========================================="
echo "Updating Backend from GitHub"
echo "========================================="

cd /var/www/backend

# Pull changes
echo "Pulling latest changes..."
git pull origin main

# Update dependencies
echo "Installing dependencies..."
composer install --optimize-autoloader --no-dev

# Run migrations
echo "Running migrations..."
php artisan migrate --force

# Clear caches
echo "Clearing caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Re-cache
echo "Caching configurations..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "========================================="
echo "Deployment completed!"
echo "========================================="

# Show last 50 lines of log
echo "\nRecent logs:"
tail -n 50 storage/logs/laravel.log
```

Make it executable:
```bash
chmod +x /var/www/backend/update.sh
```

Then just run:
```bash
/var/www/backend/update.sh
```

---

## ✅ Verification

After deployment:

1. **Test API Endpoint:**
   ```bash
   curl https://kpiapi.cclpi.com.ph/api/users
   ```

2. **Check Logs:**
   ```bash
   tail -f storage/logs/laravel.log
   ```

3. **Verify Database:**
   ```bash
   php artisan tinker
   >>> \App\Models\User::count();
   >>> exit
   ```

4. **Test in Browser:**
   - Visit: https://kpiapi.cclpi.com.ph/api/users
   - Should return JSON with user list

---

## 🔧 Troubleshooting

### Permission Errors
```bash
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### Database Connection Failed
```bash
# Check .env database credentials
php artisan config:clear
php artisan tinker
>>> DB::connection()->getPdo();
```

### 500 Errors
```bash
# Check logs
tail -f storage/logs/laravel.log

# Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

### Composer Issues
```bash
composer install --optimize-autoloader --no-dev --no-scripts
composer dump-autoload
```

---

## 📊 Your URLs

- **Production API:** https://kpiapi.cclpi.com.ph/api
- **Frontend App:** https://kpi.cclpi.com.ph
- **GitHub Repo:** https://github.com/bendamz30/kpiapi.cclpi.com.ph.git

---

## 🔑 Important Files

**Committed to GitHub:**
- ✅ `env.example` - Template (safe to commit)
- ✅ `app/Models/User.php` - Fixed timestamp bug
- ✅ `deploy.sh` & `deploy.bat` - Deployment scripts
- ✅ `Dockerfile` & `docker/` - Docker configs

**NOT in GitHub (must configure on server):**
- ❌ `.env` - Contains passwords and secrets
- ❌ `vendor/` - Will be installed via composer
- ❌ `storage/logs/` - Log files

---

**Last Push:** October 2025  
**Commit:** Fix profile picture timestamp bug and add deployment configurations

