<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // Allow all attributes to be mass assignable
    protected $guarded = [];

    // Hidden fields from array or JSON output
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Automatically cast these fields
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Add computed full_name attribute automatically
    protected $appends = ['full_name'];

    /**
     * Accessor: Get full name with title case.
     */
    public function getFullNameAttribute()
    {
        return Str::title(trim($this->first_name . ' ' . $this->last_name));
    }

    /**
     * Relationship: User belongs to a job.
     */
    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    /**
     * Relationship: Latest single attendance.
     */
    public function attendance()
    {
        return $this->hasOne(Attendance::class)->latestOfMany();
    }

    /**
     * Relationship: All attendances.
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class)->orderByDesc('created_at');
    }

    // Optional: Global scopes can be enabled here if needed in the future
    /*
    protected static function booted()
    {
        static::addGlobalScope('orderbyid', function (Builder $builder) {
            $builder->orderBy('id');
        });
    }
    */
}
