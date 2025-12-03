@extends('layouts.master')

@section('content')

    <div id="loading" class="text-center p-10 text-xl text-gray-500">
        Loading blog...
    </div>

    <div id="blog-container" class="hidden max-w-4xl mx-auto p-6 bg-white shadow-xl rounded-lg mt-10"></div>

    <div id="error" class="hidden text-center p-10 text-red-600 text-xl"></div>

@endsection



    <script>
        document.addEventListener("DOMContentLoaded", async () => {
            const token = localStorage.getItem("token");
            if (!token) {
                window.location.href = "{{ route('login') }}"
            }
            let parts = window.location.pathname.split("/").filter(Boolean);
            const slug = parts[parts.length - 1];

            const blogContainer = document.getElementById("blog-container");
            const loading = document.getElementById("loading");
            const error = document.getElementById("error");
            console.log(slug)
            try {
                // JWT for API

                const response = await fetch(`/api/blogs/${slug}`, {
                    method: "GET"
                headers: {
                        "Authorization": token ? `Bearer ${token}` : "",
                        "Accept": "application/json"
                    }
                });
                if (response) {
                    console.log("yes");
                }
                const result = await response.json();
                console.log(result)

                loading.classList.add("hidden");

                if (!result.status) {
                    error.textContent = "Blog post not found.";
                    error.classList.remove("hidden");
                    return;
                }

                const blog = result.data;
                console.log(blog)
                blogContainer.classList.remove("hidden");

                blogContainer.innerHTML = `
                <div class="flex items-center justify-between text-sm text-gray-500 mb-2">
                    <span class="font-semibold text-indigo-600 bg-indigo-50 px-3 py-1 rounded-full uppercase tracking-wider">
                        ${blog.category?.name ?? 'Uncategorized'}
                    </span>
                    <span>
                        Published on: ${new Date(blog.published_at).toLocaleDateString('en-US', {
                    month: 'short', day: '2-digit', year: 'numeric'
                })}
                    </span>
                </div>

                <div class="flex justify-between">
                    <h1 class="w-3/4 text-4xl font-extrabold text-gray-900 mb-4 leading-tight">
                        ${blog.title}
                    </h1>

                    <div class="w-1/4 py-1.5 flex justify-end gap-3 mb-6">
                        <a href="/blogs/${blog.slug}/edit"
                            class="px-4 py-2 text-gray-700 text-sm hover:text-blue-700">
                            ✏️ Edit
                        </a>

                        <form id="deleteForm" class="inline-block">
                            <button type="submit" class="px-4 py-2 text-gray-700 text-sm hover:text-red-700">
                                🗑️ Delete
                            </button>
                        </form>
                    </div>
                </div>

                ${blog.excerpt ? `
                    <p class="text-xl text-gray-600 border-l-4 border-indigo-400 pl-4 italic mb-6">
                        ${blog.excerpt}
                    </p>
                ` : ""}

                ${blog.featured_image ? `
                    <div class="mb-8 rounded-lg overflow-hidden">
                        <img src="${blog.featured_image.startsWith('http') ? blog.featured_image : '/storage/' + blog.featured_image}"
                            alt="${blog.title} featured image"
                            class="w-full h-80 object-cover">
                    </div>
                ` : ""}

                <div class="prose prose-indigo max-w-none text-gray-800 leading-relaxed space-y-6">
                    <p>${blog.content}</p>
                </div>

                ${Array.isArray(blog.tags) && blog.tags.length ? `
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <h3 class="text-lg font-bold text-gray-700 mb-3">Tags:</h3>
                        <div class="flex flex-wrap gap-2">
                            ${blog.tags.map(tag => `
                                <span class="inline-block bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-medium px-3 py-1 rounded-full cursor-pointer">
                                    #${tag}
                                </span>
                            `).join("")}
                        </div>
                    </div>
                ` : ""}
            `;

                // DELETE HANDLER
                document.getElementById("deleteForm").addEventListener("submit", async (e) => {
                    e.preventDefault();

                    if (!confirm("Are you sure you want to delete this blog permanently?")) return;

                    const deleteResponse = await fetch(`/api/blogs/${slug}`, {
                        method: "DELETE",
                        headers: {
                            "Authorization": `Bearer ${token}`,
                            "Accept": "application/json"
                        }
                    });

                    const deleteResult = await deleteResponse.json();

                    if (deleteResult.status) {
                        alert("Blog deleted successfully!");
                        window.location.href = "/blogs";
                    }
                });

            } catch (e) {
                console.log("Error:", e)
                loading.classList.add("hidden");
                error.textContent = "Something went wrong while loading the blog.";
                error.classList.remove("hidden");
            }

        });
    </script>
