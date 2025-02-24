<!-- resources/views/dashboard.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TDAC Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">

    <!-- Header -->
    <header class="bg-white shadow-md p-4 flex justify-between items-center">
        <div class="flex items-center space-x-4">
            <img src="https://thatdisabilityadventurecompany.com.au/icons/logo.webp" alt="TDAC Logo" class="w-40">
            <h1 class="text-xl font-semibold">Admin Dashboard</h1>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="bg-indigo-500 text-white px-4 py-2 rounded-md hover:bg-indigo-600">
                Logout
            </button>
        </form>
    </header>

    <div class="flex flex-grow">

        <!-- Sidebar Navigation -->
        <nav class="w-64 bg-white shadow-md p-6">
            <ul class="space-y-2">
                <li>
                    <a href="{{ route('dashboard') }}" class="text-indigo-600 font-semibold">Dashboard Home</a>
                </li>
                <li>
                    <a href="{{ route('users.index') }}" class="text-gray-600 hover:text-indigo-600">Users</a>
                </li>
                <li>
                    <a href="#" class="text-gray-600 hover:text-indigo-600">Subscription Lists</a>
                </li>
                <li>
                    <a href="#" class="text-gray-600 hover:text-indigo-600">Bulk Emails</a>
                </li>
            </ul>
        </nav>

        <!-- Main Content Area -->
        <main class="flex-grow p-6">
            <h2 class="text-2xl font-semibold mb-4">Welcome back!</h2>
            <p class="text-gray-600">Select an option from the sidebar to get started.</p>
        </main>

    </div>

</body>
</html>