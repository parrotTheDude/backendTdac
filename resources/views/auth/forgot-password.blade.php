<!-- resources/views/auth/forgot-password.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen px-4">
    <div class="bg-white p-6 sm:p-8 rounded-lg shadow-md w-full max-w-md mx-auto">

        <!-- Back to Login Link -->
        <div class="mb-2 text-center">
            <a href="{{ route('login') }}" class="text-sm text-gray-500 hover:text-gray-700">← Back to Login</a>
        </div>

        <!-- TDAC Logo -->
        <div class="mb-4 flex justify-center">
            <img src="https://thatdisabilityadventurecompany.com.au/icons/logo.webp"
                 alt="TDAC Logo"
                 class="w-48 h-auto">
        </div>

        <h2 class="text-xl sm:text-2xl font-semibold text-center mb-6">Forgot Password</h2>

        @if(session('status'))
            <div class="text-green-500 mb-4 text-center">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="mb-4">
                <input name="email" type="email" required placeholder="Email"
                    class="w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-indigo-500">
                @error('email')
                    <div class="text-red-500 mt-2 text-sm">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit"
                class="w-full bg-indigo-600 text-white py-2 rounded-md hover:bg-indigo-700 transition">
                Send Reset Link
            </button>
        </form>

    </div>
</body>
</html>