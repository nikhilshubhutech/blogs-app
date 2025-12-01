<?php

namespace App\Models\Blogs;

use Illuminate\Database\Eloquent\Model;

class BlogCategory extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'status',
    ];

    public function blogs()
    {
        return $this->hasMany(Blog::class);
    }
}