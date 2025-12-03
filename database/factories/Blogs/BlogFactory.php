<?php

namespace Database\Factories\Blogs;

use App\Models\Blogs\BlogCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BlogFactory extends Factory
{
    public function definition(): array
    {
        $title = $this->faker->sentence;

        // Generate a real image inside storage/app/public/blogs
        $imageName = uniqid().'.jpg';
        $fullPath = storage_path('app/public/blogs/'.$imageName);

        copy('https://picsum.photos/800/600', $fullPath);

        return [
            'blog_category_id' => BlogCategory::inRandomOrder()->value('id'),
            'user_id' => User::inRandomOrder()->value('id'),
            'title' => $title,
            'slug' => Str::slug($title).'-'.Str::random(5),
            'excerpt' => $this->faker->paragraph,
            'content' => $this->faker->paragraphs(5, true),
            'featured_image' => "blogs/" . $imageName,
            'tags' => json_encode($this->faker->words(3)),
            'published_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
