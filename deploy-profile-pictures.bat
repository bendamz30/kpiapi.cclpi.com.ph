@echo off
echo 🚀 Starting Profile Picture Production Deployment...

REM 1. Create storage link if it doesn't exist
echo 📁 Creating storage link...
php artisan storage:link

REM 2. Set proper permissions for storage directory (Windows)
echo 🔐 Setting storage permissions...
icacls "storage\app\public" /grant Everyone:F /T
icacls "public\storage" /grant Everyone:F /T

REM 3. Clear and cache configuration
echo ⚙️ Caching configuration...
php artisan config:clear
php artisan config:cache

REM 4. Clear and cache routes
echo 🛣️ Caching routes...
php artisan route:clear
php artisan route:cache

REM 5. Clear and cache views
echo 👁️ Caching views...
php artisan view:clear
php artisan view:cache

REM 6. Optimize for production
echo ⚡ Optimizing for production...
php artisan optimize

REM 7. Test profile picture URLs
echo 🧪 Testing profile picture configuration...
php artisan tinker --execute="echo 'Testing profile picture URLs...'; $user = \App\Models\User::whereNotNull('profile_picture')->first(); if ($user) { echo 'Profile picture URL: ' . $user->profile_picture_url . PHP_EOL; echo 'File exists: ' . ($user->hasValidProfilePicture() ? 'Yes' : 'No') . PHP_EOL; } else { echo 'No users with profile pictures found.' . PHP_EOL; }"

REM 8. Create default avatar directory if it doesn't exist
echo 🖼️ Checking for default avatar...
if not exist "public\images" (
    echo Creating default avatar directory...
    mkdir "public\images"
    echo ⚠️ Please add a default-avatar.png file to public\images\
)

echo ✅ Profile Picture deployment completed!
echo.
echo 📋 Next steps:
echo 1. Ensure your .env file has correct APP_URL for production
echo 2. Set PROFILE_PICTURE_USE_HTTPS=true in production
echo 3. Add a default-avatar.png to public\images\ if needed
echo 4. Test profile picture uploads and display
echo.
echo 🔗 Test URLs:
echo - Storage link: %CD%\public\storage -> %CD%\storage\app\public
echo - Profile pictures: %CD%\storage\app\public\profile_pictures\
pause
