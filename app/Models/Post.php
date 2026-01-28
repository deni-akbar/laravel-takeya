<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'content',
        'published_at',
        'is_draft',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isDraft()
    {
        return is_null($this->published_at);
    }

    public function isScheduled()
    {
        return $this->published_at && $this->published_at > now();
    }

    public function scopeActive($query)
    {
        return $query
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function isPublished(): bool
    {
        return $this->published_at !== null
            && $this->published_at <= now();
    }
}
