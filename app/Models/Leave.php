<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Leave extends Model
{
    use HasFactory;

    // Fillable fields
    protected $fillable = [
        'user_id',
        'from_date',
        'to_date',
        'reason',
        'status',
        'number_of_days'
    ];

    // Cast dates to Carbon instances
    protected $casts = [
        'from_date' => 'date',
        'to_date' => 'date',
    ];

    // Leave status constants
    public const STATUS_PENDING = 'Pending';
    public const STATUS_APPROVED = 'Approved';
    public const STATUS_REJECTED = 'Rejected';

    // Relationship: Leave belongs to User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Optional accessor to auto-calculate leave days (if needed)
    public function getCalculatedDaysAttribute()
    {
        if ($this->from_date && $this->to_date) {
            return $this->from_date->diffInDays($this->to_date) + 1;
        }

        return 0;
    }
}
