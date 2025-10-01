-- Fix profile picture paths in the database
-- Update users to point to the actual files in storage

-- First, let's see what's currently in the database
SELECT userId, name, profile_picture FROM users WHERE profile_picture IS NOT NULL;

-- Update the profile picture paths to point to the actual files
-- Based on what we found in storage: 1758186032_alvin.jpg and 1758186167_TIN.jpg

-- Update users to use the available profile pictures
UPDATE users 
SET profile_picture = 'profile_pictures/1758186032_alvin.jpg' 
WHERE userId = 129 AND name = 'Aljun Arpilleda';

-- You can add more updates here for other users
-- UPDATE users 
-- SET profile_picture = 'profile_pictures/1758186167_TIN.jpg' 
-- WHERE userId = [other_user_id];

-- Verify the updates
SELECT userId, name, profile_picture FROM users WHERE profile_picture IS NOT NULL;
