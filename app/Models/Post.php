<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Post extends Model
{
    use HasFactory;
    protected $fillable = [
        'publisher_id',
        'title',
        'content',
        'image',
        'post_likes_count',
        'post_dislikes_count',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function publisher() : BelongsTo
    {
        return $this->belongsTo(Trainer::class, 'publisher_id', 'id');
    }

    public function likes() : HasMany
    {
        return $this->hasMany(PostLike::class, 'post_id', 'id');
    }

    public function dislikes() : HasMany
    {
        return $this->hasMany(PostDislike::class, 'post_id', 'id');
    }
}
