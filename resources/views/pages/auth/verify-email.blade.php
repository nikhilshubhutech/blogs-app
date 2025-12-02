@extends('layouts.master')
@section('content')
    <div class="max-w-3xl mx-auto mt-16 text-gray-700">
        <h3 class="text-5xl text-center mb-12 font-semibold">Verify your email</h3>

        <form id="emailVerifyForm" class="flex flex-col gap-8 text-2xl px-6 md:px-0">

            <input type="hidden" name="email" id="email">

            <!-- otp field -->
            <div class=" w-full items-center gap-2">
                <label class="text-center" for="otp">Enter the code sent to your email address:</label><br>
                <input class="w-full md:w-4/5 border border-gray-600 p-2 rounded-lg mt-5" type="text" name="otp" id="otp">
            </div>
            <div id="otpError" class="text-red-500 text-sm ml-2 md:ml-[20%]"></div>


            <!-- verify button -->
            <button type="submit"
                class="bg-gray-700 text-white w-44 md:w-40 rounded-xl mx-auto py-3 mt-4 hover:bg-gray-800 transition-colors">
                Verify
            </button>
        </form>
    </div>
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('emailVerifyForm');
        const otpError = document.getElementById('otpError');

        const email = sessionStorage.getItem('verificationEmail');
        const emailInput = document.getElementById('email');
        if (email) {
            emailInput.value = email
        } else {
            console.warn("Cannot get email.");
        }
        form.addEventListener('submit', async function (e) {
            e.preventDefault();
            otpError.innerText = '';
            const data = {
                otp: document.getElementById('otp').value,
                email: document.getElementById('email').value,
            };

            try {
                const response = await fetch('{{ route("verify.email") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (response.ok) {
                    sessionStorage.setItem('successMessage', result.message || 'Email verified successfully!');
                    window.location.href = "{{ route('login') }}";
                } else if (response.status === 422) {
                    if (result.errors && result.errors.otp) {
                        otpError.innerText = result.errors.otp[0];
                    }

                } else {
                    otpError.innerText = result.message || 'Something went wrong while verification!';
                }

            } catch (error) {
                otpError.innerText = 'Something went wrong!';
            }
            sessionStorage.removeItem('verificationEmail');
        });
    });
</script>