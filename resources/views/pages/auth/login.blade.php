@extends('layouts.master')
@section('content')
    <div class="max-w-3xl mx-auto mt-16 text-gray-700">
        <h3 class="text-5xl text-center mb-12 font-semibold">Login</h3>

        <!-- General messages -->
        <div id="formMessage" class="text-red-500 text-center text-xl mb-4"></div>
        <div id="successMessage" class="text-green-500 text-center text-xl mb-4"></div>

        <form id="loginForm" class="flex flex-col gap-6 px-6 md:px-0">

            <!-- Email -->
            <div class="flex flex-col md:flex-row items-center gap-2">
                <label class="w-full md:w-1/5 text-left" for="email">Email:</label>
                <input class="w-full md:w-4/5 border border-gray-600 p-2 rounded-lg" type="email" name="email" id="email">
            </div>
            <span id="emailError" class="text-red-500 text-sm ml-0 md:ml-[20%] block mt-1"></span>

            <!-- Password -->
            <div class="flex flex-col md:flex-row items-center gap-2">
                <label class="w-full md:w-1/5 text-left" for="password">Password:</label>
                <input class="w-full md:w-4/5 border border-gray-600 p-2 rounded-lg" type="password" name="password"
                    id="password">
            </div>
            <span id="passwordError" class="text-red-500 text-sm ml-0 md:ml-[20%] block mt-1"></span>

            <!-- Submit -->
            <button type="submit"
                class="bg-gray-700 text-white w-44 md:w-40 rounded-xl mx-auto py-3 mt-4 hover:bg-gray-800 transition-colors">
                Login
            </button>
        </form>
    </div>
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function () {

        const form = document.getElementById('loginForm');
        const formMessage = document.getElementById('formMessage');
        const successMessage = document.getElementById('successMessage');

        // Show success message (from registration or email verification)
        const msg = sessionStorage.getItem('successMessage');
        if (msg) {
            successMessage.innerText = msg;
            sessionStorage.removeItem('successMessage');
            setTimeout(() => successMessage.innerText = '', 3000);
        }

        form.addEventListener('submit', async function (e) {
            e.preventDefault();

            // clear old messages
            formMessage.innerText = '';
            document.getElementById('emailError').innerText = '';
            document.getElementById('passwordError').innerText = '';

            const data = {
                email: document.getElementById('email').value,
                password: document.getElementById('password').value
            };

            try {
                const response = await fetch('{{ route("loginPost") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (response.ok) {
                    // success login

                    localStorage.setItem('token', result.token);
                    localStorage.setItem('userId', result.user.id);

                    // console.log(result.user)
                    sessionStorage.setItem('successMessage', result.message || 'Login successful!');
                    window.location.href = "{{ route('home') }}";

                } else {
                    // top error message
                    formMessage.innerText = result.message || 'Login failed';

                    // email field-specific error (your controller only returns this one)
                    if (result.errors && result.errors.email) {
                        document.getElementById('emailError').innerText = result.errors.email[0];
                    }
                }

            } catch (error) {
                console.error(error);
                formMessage.innerText = 'Something went wrong!';
            }
        });

    });
</script>