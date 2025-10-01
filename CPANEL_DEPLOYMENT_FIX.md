# cPanel Deployment Configuration Fixed ✅

## Issue

Your `.cpanel.yml` file had incomplete commands, causing deployment to fail:

```yaml
❌ BEFORE (Broken):
- /bin/cp -r *      # Missing destination!
- cd                # Missing directory!
```

**Error shown in cPanel:**
> "The system cannot deploy. For deployment, ensure that your repository meets the following requirements:
> 1. A valid .cpanel.yml file exists"

---

## ✅ Fixed

Updated `.cpanel.yml` with complete, working commands:

```yaml
✅ AFTER (Fixed):
---
deployment:
  tasks:
    - export DEPLOYPATH=/home/cclplwt/kpiapi.cclpi.com.ph/
    - /bin/cp -R * $DEPLOYPATH
    - cd $DEPLOYPATH
    - /bin/composer install --no-dev --optimize-autoloader
    - /bin/php artisan migrate --force
    - /bin/php artisan config:cache
    - /bin/php artisan route:cache
    - /bin/php artisan view:cache
    - /bin/chmod -R 755 storage bootstrap/cache
```

---

## 🚀 How to Deploy Backend Now

### Step 1: Update from Remote

In cPanel → **Git Version Control**:

1. Find your backend repository
2. Click **"Update from Remote"** button (not Deploy yet)
3. This pulls the latest code including fixed `.cpanel.yml`

### Step 2: Deploy

1. Click **"Deploy HEAD Commit"** button
2. cPanel will now run the deployment tasks automatically:
   - Copy files to deployment path
   - Install composer dependencies
   - Run migrations
   - Cache configurations
   - Set permissions

3. Watch for success message

---

## What the Deployment Does

When you click "Deploy HEAD Commit", cPanel automatically:

| Task | Command | Purpose |
|------|---------|---------|
| 1. Set deploy path | `export DEPLOYPATH=...` | Define where to deploy |
| 2. Copy files | `/bin/cp -R * $DEPLOYPATH` | Copy all repo files |
| 3. Navigate | `cd $DEPLOYPATH` | Go to deployment directory |
| 4. Install packages | `composer install --no-dev` | Install PHP dependencies |
| 5. Run migrations | `php artisan migrate --force` | Update database |
| 6. Cache config | `php artisan config:cache` | Optimize configuration |
| 7. Cache routes | `php artisan route:cache` | Optimize routing |
| 8. Cache views | `php artisan view:cache` | Optimize views |
| 9. Set permissions | `chmod -R 755 storage` | Fix file permissions |

---

## 🔄 Future Deployments

After this fix, your workflow is:

### Every Time You Make Changes:

**Local Machine:**
```bash
cd C:\xampp\htdocs\salesdashboard\backend
git add .
git commit -m "Your changes"
git push origin main
```

**cPanel:**
1. Git Version Control → **"Update from Remote"**
2. Click **"Deploy HEAD Commit"**
3. Done! ✅

---

## ✅ Verify Deployment

After deploying, check:

1. **Visit:** https://kpiapi.cclpi.com.ph/api/users
2. **Should return:** JSON with user list
3. **Check logs:** `tail -f storage/logs/laravel.log` (in Terminal)

---

## 🚨 Troubleshooting

### Deployment Still Fails

**Check:**
1. Path is correct: `/home/cclplwt/kpiapi.cclpi.com.ph/`
2. You have write permissions
3. `.env` file exists in deployment path
4. Composer is available

**If path is different:**
Edit `.cpanel.yml` line 4 with your actual path:
```yaml
- export DEPLOYPATH=/home/YOUR_USERNAME/YOUR_PATH/
```

### How to Find Your Deployment Path

**In cPanel Terminal:**
```bash
pwd
# Shows current path, e.g., /home/cclplwt

# Your deployment path should be:
# /home/cclplwt/kpiapi.cclpi.com.ph/
# or wherever your subdomain document root is
```

**Or in cPanel → Domains:**
- Look at the "Document Root" for kpiapi.cclpi.com.ph subdomain
- Use the parent directory of `/public`
- If document root is `/home/cclplwt/kpiapi.cclpi.com.ph/public`
- Then deployment path is `/home/cclplwt/kpiapi.cclpi.com.ph/`

---

## 📝 What Changed

**File:** `backend/.cpanel.yml`

| Line | Before | After | Fix |
|------|--------|-------|-----|
| 5 | `/bin/cp -r * ` | `/bin/cp -R * $DEPLOYPATH` | Added destination |
| 6 | `cd ` | `cd $DEPLOYPATH` | Added directory |
| 8 | (missing) | `php artisan migrate --force` | Added migrations |
| 12 | (missing) | `chmod -R 755 storage bootstrap/cache` | Added permissions |

---

## 🎯 Now You Can Use cPanel Deployment!

✅ **One-Click Deployment** - Click "Deploy HEAD Commit" button  
✅ **Automatic Tasks** - Composer, migrations, caching all run automatically  
✅ **No Terminal Needed** - Everything through cPanel interface  
✅ **Version Control** - Every deployment is tracked  

---

**Status:** ✅ Fixed and pushed to GitHub  
**Next Step:** Click "Update from Remote" then "Deploy HEAD Commit" in cPanel

🎉 **Your backend can now deploy automatically through cPanel!**

