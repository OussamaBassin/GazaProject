<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
<<<<<<< HEAD
=======
    use HasApiTokens, HasFactory, Notifiable;
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
    public function commentedArticles()
    {
        return $this->hasManyThrough(Article::class , Comment::class ,
            'user_id' , 'id' , 'id' , 'article_id')
            ->distinct();
    }
    public function favorites()
    {
        return $this->belongsToMany(Article::class, 'favorites' , 'user_id', 'article_id');
    }


>>>>>>> a411296 (nearly there)
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'favorites',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
