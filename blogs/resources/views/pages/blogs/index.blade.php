@extends('layouts.master')
@section('content')
    @if (session('success'))
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-4">
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
                <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                    <svg class="fill-current h-6 w-6 text-green-500 cursor-pointer" role="button"
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
    <div class="text-6xl text-center font-medium">Blogs</div>
    <div class="max-w-7xl mx-auto px-6 py-10">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($blogs as $blog)

                <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition relative">
                    <a href="{{ route('blogs.show', $blog->slug) }}">
                        <img src="{{ asset('storage/' . $blog->featured_image) }}" class="w-full h-48 object-cover"
                            alt="Failed to load image" />
                        <div class="p-5">
                            <span
                                class="px-3 py-1 text-xs font-semibold bg-blue-100 text-blue-600 rounded-full">{{ $blog->category->name }}</span>
                            <h2 class="text-xl font-semibold mt-3">{{ $blog->title }}</h2>
                            <p class="text-gray-500 text-sm mt-1">
                                {{ $blog->excerpt }}
                            </p>
                            <div class="flex items-center gap-3 mt-4">
                                <img src="https://i.pravatar.cc/40?img=1" class="w-10 h-10 rounded-full" />
                                <div>
                                    <p class="text-sm font-semibold">Carrie Brewer</p>
                                    <p class="text-xs text-gray-400">{{ $blog->created_at }}</p>
                                </div>
                            </div>
                        </div>
                    </a>
                    <a href="{{ route('blogs.edit', $blog->slug) }}"
                        class="absolute bottom-8  right-3 text-gray-500 hover:text-blue-600 transition" title="Edit blog">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15.232 5.232l3.536 3.536M16.768 3.768l3.536 3.536m-.707-.707l-9.193 9.193L7 17l1.414-3.404 9.193-9.193z" />
                        </svg>
                    </a>
                </div>
            @endforeach
        </div>
        <div class="mt-12">
            {{ $blogs->links() }}
        </div>
        <div>
            <a href="{{ route('blogs.create') }}"
                class="w-48 mt-20 flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">Add
                blog</a>
        </div>
    </div>




@endsection