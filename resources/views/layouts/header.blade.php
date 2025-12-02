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
                    <div id="navAuthenticated" style="display:none;">
                        <a href=""></a>
                        <a href="#" onclick="logout()">Logout</a>
                    </div>
                    <div id="navGuest" class="" style="display:none;">
                        <a class="text-left mr-10" href="{{ route('login') }}">Login</a>
                        <a class="text-right"  href="{{ route('register') }}">Register</a>
                    </div>
                </div>
            </div>
        </header>


        <script>
            const token = localStorage.getItem('token');

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

        </script>