<!-- resources/views/account/home.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title>TDAC Account Home</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-2xl font-semibold mb-6">
            Welcome, {{ Auth::user()->name }}
        </h1>

        @if(in_array(Auth::user()->user_type, ['master','super-admin']))
            <p class="mb-4 text-green-600">
                You have elevated privileges. You can access the admin area below:
            </p>
            <!-- Link to the dashboard route by name -->
            <a href="{{ route('dashboard') }}"
               class="bg-indigo-500 text-white px-4 py-2 rounded hover:bg-indigo-600">
               Go to Dashboard
            </a>
        @else
            <p class="mb-4">
                You have a normal user account. Enjoy your stay!
            </p>
        @endif
    </div>
</body>
</html>