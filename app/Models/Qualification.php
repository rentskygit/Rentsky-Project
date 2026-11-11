<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Qualification extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'data', 'last_edited_by', 'submitted_at'];

    protected $casts = [
        'data'         => 'array',
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

    public function isVoucher(): bool
    {
        return (bool) ($this->data['has_voucher'] ?? false);
    }

    public function isWalkIn(): bool
    {
        return $this->user_id === null;
    }

    public function getFullNameAttribute(): string
    {
        $name = $this->data['name'] ?? '';
        $last = $this->data['last_name'] ?? '';
        return trim($name . ' ' . $last);
    }

    public function getEmailAttribute(): ?string
    {
        $email = $this->data['email'] ?? null;
        if (!empty($email)) return $email;

        return $this->relationLoaded('user') ? $this->user?->email : null;
    }

    public function getDisplayNameAttribute(): string
    {
        if ($this->full_name !== '') return $this->full_name;

        return ($this->relationLoaded('user') ? $this->user?->name : null) ?? 'Walk-in';
    }
}