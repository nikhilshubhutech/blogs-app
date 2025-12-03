<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Blogs\BlogCategory;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class BlogFactory extends Factory
{
    public function definition(): array
    {
        $title = $this->faker->sentence;

        // Generate a real image inside storage/app/public/blogs
        $imagePath = $this->faker->image(
            storage_path('app/public/blogs'), // Directory to save image
            800,
            600,
            null, // category
            false // return only filename
        );

        return [
            'blog_category_id' => BlogCategory::inRandomOrder()->value('id'),
            'user_id' => User::inRandomOrder()->value('id'),
            'title' => $title,
            'slug' => Str::slug($title) . '-' . Str::random(5),
            'excerpt' => $this->faker->paragraph,
            'content' => $this->faker->paragraphs(5, true),
            'featured_image' => $imagePath,  // store only filename
            'tags' => json_encode($this->faker->words(3)),
            'published_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
