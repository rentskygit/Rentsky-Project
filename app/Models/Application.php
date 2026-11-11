<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'data', 'last_edited_by', 'submitted_at'];

    protected $casts = [
        'data' => 'array',
        'submitted_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lastEditor()
    {
        return $this->belongsTo(User::class, 'last_edited_by');
    }

    public function getFullNameAttribute(): string
    {
        $first = $this->data['legal_first_name'] ?? '';
        $last = $this->data['last_name'] ?? '';
        return trim($first . ' ' . $last);
    }
}