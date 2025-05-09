<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Testing\Fluent\Concerns\Has;

class Article extends Model
{
    use HasFactory; 

    protected $fillable = [
        'title',
        'content',
        'author',
        'image',
        'link',
    ];
    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'favorites');
    }
    
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

}
