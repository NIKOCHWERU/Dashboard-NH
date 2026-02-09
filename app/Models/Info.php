<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Info extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'content', 'image', 'is_public', 'expires_at', 'created_by'];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function scopeActive($query)
    {
        return $query->where(function($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
