<?php

namespace App\Models;
use App\Models\User; // <--- បន្ថែមបន្ទាត់នេះ ដើម្បីអោយប្រាកដថាវាស្គាល់

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $guarded = [];

    // relationship this child of user 
    // ដើម្បីដឹងថា Post នេះជារបស់ User មួយណាអ្នក Post
    // ទំនាក់ទំនងទៅកាន់ User (Post ជារបស់ User ណាម្នាក់)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ទំនាក់ទំនងទៅកាន់ Comment (Post មួយ មាន Comments ច្រើន)
    // ឈ្មោះ function នេះហើយដែល Controller កំពុងតែរក
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    // ទំនាក់ទំនងទៅកាន់ Like (Post មួយ មាន Likes ច្រើន)
    public function likes()
    {
        return $this->hasMany(Like::class);
    }
}

