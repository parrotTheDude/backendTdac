<!-- resources/views/partials/sidebar.blade.php -->
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