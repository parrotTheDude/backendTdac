<!-- resources/views/account/home.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title>TDAC Account Home</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="text-center bg-white p-6 shadow-md rounded-md w-full max-w-md">
        <!-- TDAC Logo -->
        <img src="https://thatdisabilityadventurecompany.com.au/icons/logo.webp" alt="TDAC Logo" class="mx-auto mb-4 w-32">
        
        <!-- Welcome Message -->
        <h1 class="text-2xl font-semibold mb-6">
            Welcome, {{ Auth::user()->name }}
        </h1>

        <!-- Logout Button -->
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600 transition">
                Logout
            </button>
        </form>
    </div>
</body>
</html>