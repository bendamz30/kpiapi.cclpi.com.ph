<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeletedSalesReport extends Model
{
    use HasFactory;

    protected $table = 'deleted_sales_reports';
    
    protected $fillable = [
        'original_report_id',
        'salesRepId',
        'reportDate',
        'premiumActual',
        'salesCounselorActual',
        'policySoldActual',
        'agencyCoopActual',
        'createdBy',
        'deleted_by',
        'deleted_at',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'reportDate' => 'date',
        'premiumActual' => 'decimal:2',
        'salesCounselorActual' => 'integer',
        'policySoldActual' => 'integer',
        'agencyCoopActual' => 'integer',
        'createdBy' => 'integer',
        'deleted_by' => 'integer',
        'original_report_id' => 'integer',
        'salesRepId' => 'integer'
    ];

    // Relationship with the user who deleted this record
    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by', 'userId');
    }

    // Relationship with the sales rep
    public function salesRep()
    {
        return $this->belongsTo(User::class, 'salesRepId', 'userId');
    }

    // Relationship with the user who created this record
    public function createdByUser()
    {
        return $this->belongsTo(User::class, 'createdBy', 'userId');
    }
}