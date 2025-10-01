<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $primaryKey = 'userId';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
     'name',
     'email',
     'username',
     'contact_number',
     'address',
     'profile_picture',
     'password',
     'passwordHash',
     'role',
     'regionId',
     'areaId',
     'salesTypeId',
     'deletedBy',
     'deletedAt'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'regionId' => 'integer',
        'areaId' => 'integer',
        'salesTypeId' => 'integer',
        'deletedBy' => 'integer',
    ];

    // Attributes to append to JSON
    protected $appends = ['profile_picture_url'];

    // Custom accessor to format timestamps
    public function getCreatedAtAttribute($value)
    {
        return $value ? date('Y-m-d\TH:i:s\Z', strtotime($value)) : null;
    }

    public function getUpdatedAtAttribute($value)
    {
        return $value ? date('Y-m-d\TH:i:s\Z', strtotime($value)) : null;
    }

    // Profile picture URL accessor
    public function getProfilePictureUrlAttribute()
    {
        if (!$this->profile_picture) {
            return null;
        }
        
        // Use Laravel's Storage facade to generate proper URLs
        $url = Storage::disk('public')->url($this->profile_picture);
        
        // URL encode the filename to handle spaces and special characters
        $url = $this->encodeUrlPath($url);
        
        // Ensure the URL is absolute for production deployment
        if (str_starts_with($url, '/')) {
            $baseUrl = config('app.url');
            // Remove trailing slash from base URL if present
            $baseUrl = rtrim($baseUrl, '/');
            $url = $baseUrl . $url;
        }
        
        // Force HTTPS in production if configured
        if (config('profile-pictures.url_generation.use_https', true) && 
            config('app.env') === 'production') {
            $url = str_replace('http://', 'https://', $url);
        }
        
        // Add cache busting parameter if enabled
        if (config('profile-pictures.url_generation.cache_busting', false)) {
            $separator = strpos($url, '?') !== false ? '&' : '?';
            // Get raw timestamp value to avoid issues with custom accessors
            $timestamp = $this->getOriginal('updated_at') 
                ? strtotime($this->getOriginal('updated_at')) 
                : time();
            $url .= $separator . 'v=' . $timestamp;
        }
        
        return $url;
    }
    
    // Helper method to properly encode URL paths
    private function encodeUrlPath($url)
    {
        // Parse the URL
        $parsedUrl = parse_url($url);
        
        if (isset($parsedUrl['path'])) {
            // Split the path into segments
            $pathSegments = explode('/', $parsedUrl['path']);
            
            // URL encode each segment (especially the filename)
            $encodedSegments = array_map('rawurlencode', $pathSegments);
            
            // Reconstruct the path
            $parsedUrl['path'] = implode('/', $encodedSegments);
            
            // Reconstruct the URL
            $url = $this->buildUrl($parsedUrl);
        }
        
        return $url;
    }
    
    // Helper method to build URL from parsed components
    private function buildUrl($parsedUrl)
    {
        $url = '';
        
        if (isset($parsedUrl['scheme'])) {
            $url .= $parsedUrl['scheme'] . '://';
        }
        
        if (isset($parsedUrl['host'])) {
            $url .= $parsedUrl['host'];
        }
        
        if (isset($parsedUrl['port'])) {
            $url .= ':' . $parsedUrl['port'];
        }
        
        if (isset($parsedUrl['path'])) {
            $url .= $parsedUrl['path'];
        }
        
        if (isset($parsedUrl['query'])) {
            $url .= '?' . $parsedUrl['query'];
        }
        
        if (isset($parsedUrl['fragment'])) {
            $url .= '#' . $parsedUrl['fragment'];
        }
        
        return $url;
    }
    
    // Check if profile picture file exists
    public function hasValidProfilePicture()
    {
        if (!$this->profile_picture) {
            return false;
        }
        
        return Storage::disk('public')->exists($this->profile_picture);
    }
    
    // Get profile picture with fallback
    public function getProfilePictureWithFallback()
    {
        if ($this->hasValidProfilePicture()) {
            return $this->profile_picture_url;
        }
        
        // Return fallback URL if configured
        $fallbackUrl = config('profile-pictures.fallback.default_avatar');
        if ($fallbackUrl && config('profile-pictures.fallback.enabled', true)) {
            if (str_starts_with($fallbackUrl, '/')) {
                return config('app.url') . $fallbackUrl;
            }
            return $fallbackUrl;
        }
        
        return null;
    }

    // Relationship with SalesType
    public function salesType()
    {
        return $this->belongsTo(\App\Models\SalesType::class, 'salesTypeId', 'salesTypeId');
    }
}
