<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesTarget extends Model
{
    use HasFactory;

    // If your primary key is not "id", specify it
    protected $primaryKey = 'targetId';

    // Columns that can be mass-assigned
    protected $fillable = [
        'salesRepId',
        'year',
        'premiumTarget',
        'salesCounselorTarget',
        'policySoldTarget',
        'agencyCoopTarget',
        'createdBy',
    ];

    protected $casts = [
        'salesRepId' => 'integer',
        'year' => 'integer',
        'premiumTarget' => 'float',
        'salesCounselorTarget' => 'integer',
        'policySoldTarget' => 'integer',
        'agencyCoopTarget' => 'integer',
        'createdBy' => 'integer',
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
}
