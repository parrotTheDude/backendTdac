<!-- resources/views/users/index.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - TDAC</title>
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
                    <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-indigo-600">Dashboard Home</a>
                </li>
                <li>
                    <a href="{{ route('users.index') }}" class="text-indigo-600 font-semibold">Users</a>
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
            <div class="flex items-center mb-4">

    <!-- Left section: Magnifying glass + expanding search input -->
    <div class="flex items-center space-x-2">

        <!-- Magnifying Glass Button -->
        <button id="searchToggle"
        class="p-2 bg-gray-200 text-gray-600 rounded-full hover:bg-gray-300 focus:outline-none">
    <img src="{{ asset('assets/icons/search.svg') }}" class="h-5 w-5" alt="Search Icon">
</button>

        <!-- Expanding Search Container -->
        <div id="searchContainer"
             class="overflow-hidden transition-all duration-300
                    {{ request('search') ? 'w-64' : 'w-0' }}">
            <form method="GET" action="{{ route('users.index') }}">
                <input
                    type="text"
                    name="search"
                    placeholder="Search..."
                    value="{{ request('search') }}"
                    class="p-2 border rounded-md shadow-sm focus:outline-none"
                >
            </form>
        </div>
    </div>

    <!-- Right section: Add User Button -->
    <a
        href="{{ route('users.create') }}"
        class="bg-indigo-500 text-white px-4 py-2 rounded-md hover:bg-indigo-600 transition">
        + Add New User
    </a>
</div>
            <table class="w-full bg-white shadow rounded-md overflow-hidden">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="py-3 px-4">Name</th>
                        <th class="py-3 px-4">Last Name</th>
                        <th class="py-3 px-4">Email</th>
                        <th class="py-3 px-4">User Type</th>
                        <th class="py-3 px-4">Gender</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr class="border-b hover:bg-gray-100">
                            <td class="py-3 px-4">{{ $user->name }}</td>
                            <td class="py-3 px-4">{{ $user->last_name }}</td>
                            <td class="py-3 px-4">
                                <a href="{{ route('users.show', $user->id) }}" class="text-indigo-600 hover:underline">
                                    {{ $user->email }}
                                </a>
                            </td>
                            <td class="py-3 px-4">{{ $user->user_type }}</td>
                            <td class="py-3 px-4">{{ $user->gender }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </main>

    </div>
<script>
    const searchToggle = document.getElementById('searchToggle');
    const searchContainer = document.getElementById('searchContainer');

    searchToggle.addEventListener('click', () => {
        if (searchContainer.classList.contains('w-0')) {
            searchContainer.classList.remove('w-0');
            searchContainer.classList.add('w-64'); // expand to ~16rem
        } else {
            searchContainer.classList.remove('w-64');
            searchContainer.classList.add('w-0');
        }
    });
</script>
</body>
</html>