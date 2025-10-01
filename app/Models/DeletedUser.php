<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeletedUser extends Model
{
    use HasFactory;

    protected $table = 'deleted_users';
    
    protected $fillable = [
        'original_user_id',
        'name',
        'email',
        'username',
        'contact_number',
        'address',
        'profile_picture',
        'passwordHash',
        'role',
        'regionId',
        'areaId',
        'salesTypeId',
        'deleted_by',
        'deleted_at',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'regionId' => 'integer',
        'areaId' => 'integer',
        'salesTypeId' => 'integer',
        'deleted_by' => 'integer',
        'original_user_id' => 'integer'
    ];

    // Attributes to append to JSON
    protected $appends = ['profile_picture_url'];

    // Profile picture URL accessor
    public function getProfilePictureUrlAttribute()
    {
        if (!$this->profile_picture) {
            return null;
        }
        
        return \Storage::disk('public')->url($this->profile_picture);
    }

    // Relationship with the user who deleted this record
    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by', 'userId');
    }
}