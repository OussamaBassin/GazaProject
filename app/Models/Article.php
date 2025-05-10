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
    public function favorites()
    {
        return $this->belongsToMany(User::class, 'favorites' , 'article_id', 'user_id');
    }
    
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

}
