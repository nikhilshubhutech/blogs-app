@extends('layouts.master')
@section('content')
<div class="max-w-3xl mx-auto mt-16 text-gray-700">
    <h3 class="text-5xl text-center mb-12 font-semibold">Register</h3>

    <!-- General error message -->
    <div id="formMessage" class="text-red-500 text-center text-xl mb-6"></div>

    <form id="registerForm" class="flex flex-col gap-8 text-2xl px-6 md:px-0">

        <!-- Name field -->
        <div class="flex flex-col md:flex-row items-center gap-2">
            <label class="w-full md:w-1/5 text-left" for="name">Name:</label>
            <input class="w-full md:w-4/5 border border-gray-600 p-2 rounded-lg" type="text" name="name" id="name">
        </div>
        <div id="nameError" class="text-red-500 text-sm ml-2 md:ml-[20%]"></div>

        <!-- Email field -->
        <div class="flex flex-col md:flex-row items-center gap-2">
            <label class="w-full md:w-1/5 text-left" for="email">Email:</label>
            <input class="w-full md:w-4/5 border border-gray-600 p-2 rounded-lg" type="email" name="email" id="email">
        </div>
        <div id="emailError" class="text-red-500 text-sm ml-2 md:ml-[20%]"></div>

        <!-- Password field -->
        <div class="flex flex-col md:flex-row items-center gap-2">
            <label class="w-full md:w-1/5 text-left" for="password">Password:</label>
            <input class="w-full md:w-4/5 border border-gray-600 p-2 rounded-lg" type="password" name="password" id="password">
        </div>
        <div id="passwordError" class="text-red-500 text-sm ml-2 md:ml-[20%]"></div>

        <!-- Submit button -->
        <button type="submit" class="bg-gray-700 text-white w-44 md:w-40 rounded-xl mx-auto py-3 mt-4 hover:bg-gray-800 transition-colors">
            Register
        </button>
    </form>
</div>
@endsection

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('registerForm');
    const messageBox = document.getElementById('formMessage');

    form.addEventListener('submit', async function (e) {
        e.preventDefault();

        messageBox.innerText = '';
        ['name', 'email', 'password'].forEach(field => {
            document.getElementById(field + 'Error').innerText = '';
        });

        const data = {
            name: document.getElementById('name').value,
            email: document.getElementById('email').value,
            password: document.getElementById('password').value,
        };

        try {
            const response = await fetch('{{ route("registerPost") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (response.ok) {
                sessionStorage.setItem('successMessage', result.message || 'Registration successful!');
                window.location.href = result.redirect;
            } else if (response.status === 422) {
                const errors = result.errors;
                for (const field in errors) {
                    const errorElement = document.getElementById(field + 'Error');
                    if (errorElement) {
                        errorElement.innerText = errors[field][0];
                    }
                }
            } else {
                messageBox.innerText = result.message || 'Something went wrong!';
            }

        } catch (error) {
            console.error(error);
            messageBox.classList.remove('text-green-500');
            messageBox.classList.add('text-red-500');
            messageBox.innerText = 'Something went wrong!';
        }
    });
});
</script>
