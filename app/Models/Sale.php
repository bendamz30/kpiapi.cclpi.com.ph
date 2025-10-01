<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Sale extends Model
{
    use HasFactory;

    protected $primaryKey = 'reportId';
    
    protected $fillable = [
        'salesRepId',
        'reportDate',
        'premiumActual',
        'salesCounselorActual',
        'policySoldActual',
        'agencyCoopActual',
        'createdBy',
        'deletedBy',
    ];

    protected $dates = [
        'reportDate',
        'deletedAt',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'premiumActual' => 'float',
        'salesCounselorActual' => 'integer',
        'policySoldActual' => 'integer',
        'agencyCoopActual' => 'integer',
        'createdBy' => 'integer',
        'deletedBy' => 'integer',
    ];

    // Custom accessor to format timestamps
    public function getCreatedAtAttribute($value)
    {
        return $value ? date('Y-m-d\TH:i:s\Z', strtotime($value)) : null;
    }

    public function getUpdatedAtAttribute($value)
    {
        return $value ? date('Y-m-d\TH:i:s\Z', strtotime($value)) : null;
    }

    // Relationship with User (Sales Representative)
    public function salesRep()
    {
        return $this->belongsTo(User::class, 'salesRepId', 'userId');
    }

    // Relationship with User (Creator)
    public function creator()
    {
        return $this->belongsTo(User::class, 'createdBy', 'userId');
    }
}
