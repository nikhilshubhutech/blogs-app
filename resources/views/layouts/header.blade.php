<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BlogifyHub</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
</head>

<body>
    <div class="min-h-screen bg-gray-50 flex flex-col">
        <header class="px-32 py-5 bg-gray-200 shadow-2xl mb-10">
            <div class="flex h-auto justify-between">
                <div class="h-full flex">
                    <a class="text-3xl text-gray-600" href="/">BlogifyHub</a>
                </div>
                <div>
                    <nav class="flex items-center gap-15 justify-between">
                        <a class="text-xl text-gray-600 hover:text-gray-900" href="/">Home</a>
                        <a class="text-xl text-gray-600 hover:text-gray-900" href="{{ route('blogs.index') }}">Blogs</a>
                        <a class="text-xl text-gray-600 hover:text-gray-900" href="/about">About</a>
                        <a class="text-xl text-gray-600 hover:text-gray-900" href="/contact">Contact</a>
                    </nav>
                </div>
                <div class="text-xl">
                    <div id="navAuthenticated" class="relative" style="display:none;">
                        <!-- Profile Icon -->
                        <button id="profileBtn" class="flex items-center focus:outline-none">

                        </button>

                        <!-- Dropdown Menu -->
                        <div id="profileDropdown"
                            class="hidden absolute right-0 mt-3 w-48 bg-white shadow-lg rounded-lg py-2 border">
                            <a href="/profile" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                                View Profile
                            </a>
                            <a href="{{ route('blogs.index') }}"
                                class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                                My Blogs
                            </a>
                            <button onclick="logout()"
                                class="w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-100">
                                Logout
                            </button>
                        </div>
                    </div>

                    <div id="navGuest" class="" style="display:none;">
                        <a class="text-left mr-10" href="{{ route('login') }}">Login</a>
                        <a class="text-right" href="{{ route('register') }}">Register</a>
                    </div>
                </div>
            </div>
        </header>


        <script>
            const token = localStorage.getItem('token');
            // console.log("TOKEN FROM LOCALSTORAGE:", token);
            // let userDetail;
            // async function fetchUser(url = '/api/me') {
            //     try {
            //         const response = await fetch(url, {
            //             headers: {
            //                 'Authorization': `Bearer ${token}`,
            //                 'Accept': 'application/json'
            //             }
            //         });
            //         const result = await response.json();
            //         userDetail = result.user;
            //         console.log(userDetail);
            //     } catch (e) {
            //         console.log(e)
            //     }

            // }

            // fetchUser()

            // const user = localStorage.getItem('user')
            // console.log(user.name)
            if (token) {
                document.getElementById('navAuthenticated').style.display = 'block';
                document.getElementById('navGuest').style.display = 'none';
            } else {
                document.getElementById('navAuthenticated').style.display = 'none';
                document.getElementById('navGuest').style.display = 'block';
            }


            function logout() {
                localStorage.removeItem('token');
                window.location.href = '{{ route("login") }}';
            }



            const profileBtn = document.getElementById('profileBtn');
            profileBtn.innerHTML = `<img src="https://ui-avatars.com/api/?name=user&background=4b5563&color=fff"
                                class="w-10 h-10 rounded-full cursor-pointer">`
            const profileDropdown = document.getElementById('profileDropdown');

            if (profileBtn) {
                profileBtn.addEventListener('click', () => {
                    profileDropdown.classList.toggle('hidden');
                });

                // Close dropdown when clicking outside
                document.addEventListener('click', (event) => {
                    if (!profileBtn.contains(event.target) && !profileDropdown.contains(event.target)) {
                        profileDropdown.classList.add('hidden');
                    }
                });
            }
        </script>