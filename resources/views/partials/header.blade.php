<!-- resources/views/partials/header.blade.php -->
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