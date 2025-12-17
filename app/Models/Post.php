<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $guarded = [];

    // relationship this child of user 
    // ដើម្បីដឹងថា Post នេះជារបស់ User មួយណាអ្នក Post
    public function user(){
        return $this->belongsTo(User::class);
    }
    
    public function like(){
        return $this->hasMany(Like::class);
    }
    public function comment(){
        return $this->hasMany(Comment::class);
    }
}

