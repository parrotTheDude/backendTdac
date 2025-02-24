<!-- resources/views/auth/login.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TDAC Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen px-4">
    <div class="bg-white p-6 sm:p-8 rounded-lg shadow-md w-full max-w-md mx-auto">

        <!-- TDAC Logo -->
        <div class="mb-4 flex justify-center">
            <img src="https://thatdisabilityadventurecompany.com.au/icons/logo.webp"
                 alt="TDAC Logo"
                 class="w-48 sm:w-48 h-auto">
        </div>

        <h1 class="text-xl sm:text-2xl font-semibold text-center mb-6">Login</h1>
        
        @if(session('status'))
            <div class="text-green-500 mb-4 text-center">{{ session('status') }}</div>
        @endif

        @if($errors->any())
            <div class="text-red-500 mb-4 text-center">
                {{$errors->first()}}
            </div>
        @endif

        <form method="POST" action="{{ route('login.submit') }}">
            @csrf

            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" id="email" name="email" required autofocus
                    class="mt-1 block w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-indigo-500">
            </div>

            <div class="mb-2">
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" id="password" name="password" required
                    class="mt-1 block w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-indigo-500">
            </div>

            <!-- Forgot Password Link -->
            <div class="mb-4 text-right">
                <a href="{{ route('password.request') }}" class="text-sm text-gray-500 hover:text-gray-700">
                    Forgot password?
                </a>
            </div>

            <button type="submit"
                class="w-full bg-indigo-600 text-white py-2 rounded-md hover:bg-indigo-700 transition">
                Login
            </button>
        </form>
    </div>
</body>
</html>

