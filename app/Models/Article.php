<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{

    protected $fillable = [
        'title',
        'content',
        'author',
        'image',
        'link',
        'user_id',
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
