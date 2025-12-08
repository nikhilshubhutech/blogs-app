@extends('layouts.master')
@section('content')
    <div class="text-6xl text-center font-medium mt-8">Blogs</div>

    <!-- Success message -->
    <div id="successMessageContainer" class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-4 hidden">
        <div id="successMessage" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative"
            role="alert">
            <span class="block sm:inline"></span>
            <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                <svg class="fill-current h-6 w-6 text-green-500 cursor-pointer" role="button"
                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                    onclick="this.parentElement.parentElement.parentElement.style.display='none';">
                    <title>Close</title>
                    <path
                        d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z" />
                </svg>
            </span>
        </div>
    </div>

    <!-- Blog container -->
    <div class="max-w-7xl mx-auto px-6 py-10">
        <div id="blogsContainer" class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Blogs will be injected here -->
        </div>

        <!-- Pagination -->
        <div id="paginationContainer" class="mt-12 flex justify-center gap-2"></div>
    </div>
@endsection

<script>
    document.addEventListener('DOMContentLoaded', async () => {
        const container = document.getElementById('blogsContainer');
        const paginationContainer = document.getElementById('paginationContainer');


        // Show session success message if available
        const sessionSuccess = sessionStorage.getItem('successMessage');
        if (sessionSuccess) {
            const successEl = document.getElementById('successMessageContainer');
            successEl.classList.remove('hidden');
            successEl.querySelector('span.block').innerText = sessionSuccess;
            sessionStorage.removeItem('successMessage');
            setTimeout(() => successEl.classList.add('hidden'), 3000);
        }

        async function fetchBlogs(url = '/api/blogs') {
            container.innerHTML = 'Loading...';
            paginationContainer.innerHTML = '';

            try {
                const response = await fetch(url, {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });
                const result = await response.json();

                if (result.status) {
                    container.innerHTML = '';
                    result.data.forEach(blog => {
                        const blogCard = document.createElement('div');
                        blogCard.className = "bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition relative";
                        blogCard.innerHTML = `
                        <a href="/blogs/${blog.slug}">
                            <img src="/storage/${blog.featured_image}" class="w-full h-48 object-cover" alt="Failed to load image" />
                            <div class="p-5">
                                <span class="px-3 py-1 text-xs font-semibold bg-blue-100 text-blue-600 rounded-full">${blog.category.name}</span>
                                <h2 class="text-xl font-semibold mt-3">${blog.title}</h2>
                                <p class="text-gray-500 text-sm mt-1">${blog.excerpt}</p>
                                <div class="flex items-center gap-3 mt-4">
                                    <img src="https://i.pravatar.cc/40?img=1" class="w-10 h-10 rounded-full" />
                                    <div>
                                        <p class="text-sm font-semibold">${blog.user.name}</p>
                                        <p class="text-xs text-gray-400">${new Date(blog.created_at).toLocaleDateString()}</p>
                                    </div>
                                </div>
                            </div>
                        </a>
                        <a href="/blogs/${blog.slug}/edit"
                            class="absolute bottom-8 right-3 text-gray-500 hover:text-blue-600 transition" title="Edit blog">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15.232 5.232l3.536 3.536M16.768 3.768l3.536 3.536m-.707-.707l-9.193 9.193L7 17l1.414-3.404 9.193-9.193z" />
                            </svg>
                        </a>
                    `;
                        container.appendChild(blogCard);
                    });

                    // Pagination links
                    // Pagination links - SCALABLE VERSION
                    if (result.data.last_page > 1) {
                        const totalPages = result.data.last_page;
                        const currentPage = result.data.current_page;
                        const maxVisiblePages = 7; // Number of pages to show (e.g., 1, 2, ..., 5, 6, 7)
                        let startPage, endPage;

                        // Logic to determine which pages to display
                        if (totalPages <= maxVisiblePages) {
                            startPage = 1;
                            endPage = totalPages;
                        } else {
                            const sidePages = Math.floor(maxVisiblePages / 2); // e.g., 3
                            startPage = Math.max(1, currentPage - sidePages);
                            endPage = Math.min(totalPages, currentPage + sidePages);

                            if (currentPage <= sidePages) {
                                endPage = maxVisiblePages;
                            } else if (currentPage + sidePages >= totalPages) {
                                startPage = totalPages - maxVisiblePages + 1;
                            }
                        }

                        // --- Previous Button ---
                        if (result.data.prev_page_url) {
                            const prevBtn = document.createElement('button');
                            prevBtn.innerText = '« Prev';
                            prevBtn.className = "px-4 py-2 border rounded text-sm hover:bg-gray-200 shadow-sm";
                            prevBtn.addEventListener('click', () => fetchBlogs(result.data.prev_page_url));
                            paginationContainer.appendChild(prevBtn);
                        }

                        // --- Render Page Numbers ---

                        // Show '1' and Ellipsis if needed
                        if (startPage > 1) {
                            const firstBtn = document.createElement('button');
                            firstBtn.innerText = '1';
                            firstBtn.className = "px-4 py-2 border rounded text-sm hover:bg-gray-200 shadow-sm";
                            firstBtn.addEventListener('click', () => fetchBlogs('/api/blogs?page=1'));
                            paginationContainer.appendChild(firstBtn);

                            if (startPage > 2) {
                                const ellipsis = document.createElement('span');
                                ellipsis.innerText = '...';
                                ellipsis.className = "px-4 py-2 text-gray-500";
                                paginationContainer.appendChild(ellipsis);
                            }
                        }


                        for (let i = startPage; i <= endPage; i++) {
                            const btn = document.createElement('button');
                            btn.innerText = i;
                            btn.className = "px-4 py-2 border rounded text-sm hover:bg-gray-200 shadow-sm transition";
                            if (i === currentPage) {
                                btn.classList.add('bg-indigo-600', 'text-white', 'border-indigo-600', 'shadow-md', 'hover:bg-indigo-700');
                            }
                            btn.addEventListener('click', () => fetchBlogs(`/api/blogs?page=${i}`));
                            paginationContainer.appendChild(btn);
                        }

                        // Show Ellipsis and 'Last Page' button if needed
                        if (endPage < totalPages) {
                            if (endPage < totalPages - 1) {
                                const ellipsis = document.createElement('span');
                                ellipsis.innerText = '...';
                                ellipsis.className = "px-4 py-2 text-gray-500";
                                paginationContainer.appendChild(ellipsis);
                            }

                            const lastBtn = document.createElement('button');
                            lastBtn.innerText = totalPages;
                            lastBtn.className = "px-4 py-2 border rounded text-sm hover:bg-gray-200 shadow-sm";
                            lastBtn.addEventListener('click', () => fetchBlogs(`/api/blogs?page=${totalPages}`));
                            paginationContainer.appendChild(lastBtn);
                        }

                        // --- Next Button ---
                        if (result.data.next_page_url) {
                            const nextBtn = document.createElement('button');
                            nextBtn.innerText = 'Next »';
                            nextBtn.className = "px-4 py-2 border rounded text-sm hover:bg-gray-200 shadow-sm";
                            nextBtn.addEventListener('click', () => fetchBlogs(result.data.next_page_url));
                            paginationContainer.appendChild(nextBtn);
                        }
                    }

                } else {
                    container.innerHTML = `<p>${result.message}</p>`;
                }

            } catch (err) {
                console.error(err);
                container.innerHTML = `<p>Something went wrong!</p>`;
            }
        }

        fetchBlogs();
    });
</script>