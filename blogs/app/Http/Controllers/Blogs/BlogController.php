<?php

namespace App\Http\Controllers\Blogs;

use App\Http\Controllers\Controller;
use App\Models\Blogs\Blog;
use App\Models\Blogs\BlogCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $blogs = Blog::with('category')->orderBy('created_at', 'desc')->paginate(12);

        return view('pages.blogs.index', compact('blogs'));
    }

    /**
     * Show the form for creating a new blog.
     */
    public function create()
    {
        $categories = BlogCategory::all();

        return view('pages.blogs._partials.form', [
            'categories' => $categories,
            'blog' => null,
        ]);
    }

    /**
     * Store new blog post.
     */
    public function store(Request $request)
    {
        return $this->storeOrUpdate($request);
    }

    /**
     * Show the form for editing the specified blog.
     */
    public function edit($slug)
    {
        $categories = BlogCategory::all();
        $blog = Blog::where('slug', $slug)->firstOrFail();

        return view('pages.blogs._partials.form', [
            'categories' => $categories,
            'blog' => $blog,
        ]);
    }

    /**
     * Update an existing blog.
     */
    public function update(Request $request, $id)
    {
        $blog = Blog::findOrFail($id);

        return $this->storeOrUpdate($request, $blog);
    }

    /**
     * Store or update logic shared for both actions.
     */
    public function storeOrUpdate(Request $request, ?Blog $blog = null)
    {
        $isCreating = is_null($blog);

        $request->validate([
            'blogCategory' => 'required|exists:blog_categories,id',
            'title' => 'required|max:255',
            'excerpt' => 'required',
            'content' => 'required',
            'image' => ($isCreating ? 'required' : 'nullable').'|image|mimetypes:image/jpeg,image/png,image/webp|max:2048',
        ]);

        if ($isCreating) {
            $blog = new Blog;
        }

        // Handle Image Upload
        $imagePath = $blog->featured_image;
        if ($request->hasFile('image')) {
            if ($blog->featured_image) {
                Storage::disk('public')->delete($blog->featured_image);
            }
            $imagePath = $request->file('image')->store('blogs', 'public');
        }

        // Convert comma-separated tags
        $tagsArray = collect(explode(',', $request->tags))
            ->map(fn ($tag) => trim($tag))
            ->filter()
            ->values();

        // Assign fields
        $blog->blog_category_id = $request->blogCategory;
        $blog->title = $request->title;
        $blog->excerpt = $request->excerpt;
        $blog->content = $request->content;
        $blog->featured_image = $imagePath;
        $blog->tags = $tagsArray;

        // Auto–generate slug only on create
        if ($isCreating) {
            $blog->slug = Str::slug($blog->title).'-'.Str::random(6);
            $blog->published_at = now();
        }

        // Save post
        $blog->save();

        $message = $isCreating
            ? '✅ Blog post created successfully!'
            : '💾 Blog post updated successfully!';

        return redirect()->route('blogs.index')->with('success', $message);
    }

    /**
     * Display blog details.
     */
    public function show($slug)
    {
        $blog = Blog::where('slug', $slug)->with('category')->firstOrFail();

        return view('pages.blogs._partials.blog-details', compact('blog'));
    }

    public function destroy($slug)
    {
        $blog = Blog::where('slug', $slug)->firstOrFail();
        $blog->delete();
        return redirect()->route('blogs.index')->with('success', 'Blog deleted successfully!');
    }
}
