@extends('layouts.master')

@section('content')
    @vite(['resources/js/script.js'])
    {{-- Error Message --}}
    @if (session('error'))
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-4">
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
                <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                    <svg class="fill-current h-6 w-6 text-red-500 cursor-pointer" role="button"
                         xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                         onclick="this.parentElement.parentElement.style.display='none';">
                        <title>Close</title>
                        <path
                            d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z" />
                    </svg>
                </span>
            </div>
        </div>
    @endif


    <h1 class="text-7xl text-center text-gray-700">
        {{ $blog ? 'Edit Blog' : 'Add New Blog' }}
    </h1>

    <div class="max-w-3xl mx-auto p-8 bg-white shadow-xl rounded-lg mt-10">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">
            {{ $blog ? 'Update Blog Post' : 'Create New Blog Post' }}
        </h2>

        {{-- FORM (CREATE + EDIT) --}}
        <form 
            action="{{ $blog ? route('blogs.update', $blog->id) : route('blogs.store') }}"
            method="POST"
            enctype="multipart/form-data"
            id="blogForm"
            class="space-y-6" >
            @csrf
            @if ($blog)
                @method('PUT')
            @endif

            {{-- CATEGORY --}}
            <div>
                <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Select category:</label>
                <select name="blogCategory" id="category"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm py-2 px-3 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}"
                            {{ old('blogCategory', $blog->blog_category_id ?? '') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                <p id="blogCategoryError" class="text-red-500 mt-1"></p>
                @error('blogCategory')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>


            {{-- TITLE --}}
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Blog Title:</label>
                <input type="text" id="title" name="title"
                    value="{{ old('title', $blog->title ?? '') }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2">
                <p id="titleError" class="text-red-500 mt-1"></p>

                @error('title')
                    <p class="text-sm text-red-600 mt-1 h-5">{{ $message }}</p>
                @enderror
            </div>


            {{-- EXCERPT --}}
            <div>
                <label for="excerpt" class="block text-sm font-medium text-gray-700 mb-1">Blog excerpt:</label>
                <input type="text" name="excerpt" id="excerpt"
                    value="{{ old('excerpt', $blog->excerpt ?? '') }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2">
                <p id="excerptError" class="text-red-500 mt-1"></p>

                @error('excerpt')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>


            {{-- CONTENT --}}
            <div>
                <label for="content" class="block text-sm font-medium text-gray-700 mb-1">Blog Content:</label>
                <textarea id="content" name="content" rows="10"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2">{{ old('content', $blog->content ?? '') }}</textarea>
                <p id="contentError" class="text-red-500 mt-1"></p>
                @error('content')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>


            {{-- EXISTING IMAGE --}}
            @if ($blog && $blog->featured_image)
                <div class="mb-2">
                    <p class="text-sm text-gray-600 mb-1">Current Image:</p>
                    <img src="{{ asset('storage/' . $blog->featured_image) }}" 
                         class="h-24 object-cover rounded shadow">
                </div>
            @endif

            {{-- IMAGE UPLOAD --}}
            <div>
                <label for="image" class="block text-sm font-medium text-gray-700 mb-1">Featured image:</label>
                <input type="file" name="image" id="image"
                    class="mt-1 block w-full text-sm text-gray-500 file:bg-indigo-50 file:text-indigo-700 file:py-2 file:px-4 file:rounded-md">
                <p id="imageError" class="text-red-500 mt-1"></p>

                @error('image')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @else
                    @if ($errors->any())
                        <p class="text-sm text-gray-500 mt-1 italic">
                            Please re-select the image if you had chosen one.
                        </p>
                    @endif
                @enderror
            </div>


            {{-- TAGS --}}
            <div>
                <label for="tags" class="block text-sm font-medium text-gray-700 mb-1">Tags (comma-separated):</label>
                <input type="text" name="tags" id="tags"
                    value="{{ old('tags', isset($blog) && is_array($blog->tags) ? implode(',', $blog->tags) : '') }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2"
                    placeholder="e.g., Laravel, PHP, Database">
                
            </div>


            {{-- SUBMIT --}}
            <div>
                <button type="submit"
                    class="w-full flex justify-center py-2 px-4 rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                    {{ $blog ? 'Update Blog' : 'Create Blog' }}
                </button>
            </div>

        </form>
    </div>

@endsection
