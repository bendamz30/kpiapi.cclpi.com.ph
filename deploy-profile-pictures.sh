#!/bin/bash

# Production Deployment Script for Profile Pictures
# This script ensures profile pictures work correctly in production

echo "🚀 Starting Profile Picture Production Deployment..."

# 1. Create storage link if it doesn't exist
echo "📁 Creating storage link..."
php artisan storage:link

# 2. Set proper permissions for storage directory
echo "🔐 Setting storage permissions..."
chmod -R 755 storage/app/public
chmod -R 755 public/storage

# 3. Clear and cache configuration
echo "⚙️ Caching configuration..."
php artisan config:clear
php artisan config:cache

# 4. Clear and cache routes
echo "🛣️ Caching routes..."
php artisan route:clear
php artisan route:cache

# 5. Clear and cache views
echo "👁️ Caching views..."
php artisan view:clear
php artisan view:cache

# 6. Optimize for production
echo "⚡ Optimizing for production..."
php artisan optimize

# 7. Test profile picture URLs
echo "🧪 Testing profile picture configuration..."
php artisan tinker --execute="
\$user = \App\Models\User::whereNotNull('profile_picture')->first();
if (\$user) {
    echo 'Profile picture URL: ' . \$user->profile_picture_url . PHP_EOL;
    echo 'File exists: ' . (\$user->hasValidProfilePicture() ? 'Yes' : 'No') . PHP_EOL;
} else {
    echo 'No users with profile pictures found.' . PHP_EOL;
}
"

# 8. Create default avatar if it doesn't exist
echo "🖼️ Checking for default avatar..."
if [ ! -f "public/images/default-avatar.png" ]; then
    echo "Creating default avatar directory..."
    mkdir -p public/images
    echo "⚠️ Please add a default-avatar.png file to public/images/"
fi

echo "✅ Profile Picture deployment completed!"
echo ""
echo "📋 Next steps:"
echo "1. Ensure your .env file has correct APP_URL for production"
echo "2. Set PROFILE_PICTURE_USE_HTTPS=true in production"
echo "3. Add a default-avatar.png to public/images/ if needed"
echo "4. Test profile picture uploads and display"
echo ""
echo "🔗 Test URLs:"
echo "- Storage link: $(pwd)/public/storage -> $(pwd)/storage/app/public"
echo "- Profile pictures: $(pwd)/storage/app/public/profile_pictures/"
