<?php

namespace App\Models;

use App\Models\Property\Property;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Showing extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'property_id',
        'showing_date',
        'showing_time',
        'status',
        'notes',
    ];

    protected $casts = [
        'showing_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'   => 'Pending',
            'confirmed' => 'Confirmed',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            default     => ucfirst($this->status),
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'pending'   => 'warning',
            'confirmed' => 'info',
            'completed' => 'success',
            'cancelled' => 'inactive',
            default     => 'neutral',
        };
    }

    public function getFormattedDateTimeAttribute(): string
    {
        return $this->showing_date->format('M j, Y') . ' · ' . $this->showing_time;
    }
}