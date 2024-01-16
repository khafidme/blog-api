<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Post extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'image',
        'title',
        'content',
    ];

    /**
     * Image accessor.
     */
    public function image(): Attribute {
        return Attribute::make(
            get: fn($image) => asset("/storage/posts/" . $image),
        );
    }
}
