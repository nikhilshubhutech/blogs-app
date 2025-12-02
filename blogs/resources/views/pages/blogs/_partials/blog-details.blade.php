@extends('layouts.master')
@section('content')
    @if($blog)
        <div class="max-w-4xl mx-auto p-6 bg-white shadow-xl rounded-lg mt-10">

            <div class="flex items-center justify-between text-sm text-gray-500 mb-2">
                <span class="font-semibold text-indigo-600 bg-indigo-50 px-3 py-1 rounded-full uppercase tracking-wider">
                    {{ $blog->category->name ?? 'Uncategorized' }}
                </span>
                <span>
                    Published on: {{ \Carbon\Carbon::parse($blog->published_at)->format('M d, Y') }}

                </span>
            </div>
            <div class="flex justify-between">
                <h1 class="w-3/4 text-4xl font-extrabold text-gray-900 mb-4 leading-tight">
                    {{ $blog->title }}
                </h1>
                <div class="w-1/4 py-1.5 flex justify-end gap-3 mb-6">
                    <a href="{{ route('blogs.edit', $blog->slug) }}"
                        class="px-4 py-2 text-gray-700 text-sm hover:text-blue-700">
                        ✏️ Edit
                    </a>
                    <form action="{{ route('blogs.destroy', $blog->slug) }}" method="POST"
                        onsubmit="return confirm('Are you sure you want to delete this blog permanently?');">
                        @csrf
                        @method('DELETE')

                        <button type="submit" class="px-4 py-2 text-gray-700 text-sm hover:text-red-700">
                            🗑️ Delete
                        </button>
                    </form>

                </div>
            </div>
            @if($blog->excerpt)
                <p class="text-xl text-gray-600 border-l-4 border-indigo-400 pl-4 italic mb-6">
                    {{ $blog->excerpt }}
                </p>
            @endif

            @if($blog->featured_image)
                <div class="mb-8 rounded-lg overflow-hidden">
                    <img src="{{ asset('storage/' . $blog->featured_image) }}" alt="{{ $blog->title }} featured image"
                        class="w-full h-80 object-cover">
                </div>
            @endif

            <div class="prose prose-indigo max-w-none text-gray-800 leading-relaxed space-y-6">
                <p>{{ $blog->content }}</p>
            </div>

            {{-- @php
                // The 'tags' field is a JSON string, so we decode it.
                $tags = $blog->tags
            @endphp --}}

            @if(is_array($blog->tags) && count($blog->tags) > 0)
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <h3 class="text-lg font-bold text-gray-700 mb-3">Tags:</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach($blog->tags as $tag)
                            <span
                                class="inline-block bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-medium px-3 py-1 rounded-full transition duration-150 ease-in-out cursor-pointer">
                                #{{ $tag }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>
    @else
        <div class="text-center p-10">
            <p class="text-red-600 text-xl">Blog post not found.</p>
        </div>
    @endif
@endsection