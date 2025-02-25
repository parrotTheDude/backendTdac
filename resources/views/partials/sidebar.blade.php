@php
    // Check current route for each major section
    $dashboardActive = request()->routeIs('dashboard');
    $usersActive = request()->routeIs('users.*');
    $subscriptionsActive = request()->routeIs('subscriptions.*');
@endphp

<!-- Sidebar Navigation -->
<nav class="w-64 bg-white shadow-md p-6">
    <ul class="space-y-2">
        <li>
            <a href="{{ route('dashboard') }}" 
               class="{{ request()->routeIs('dashboard') ? 'text-indigo-600 font-semibold' : 'text-gray-600 hover:text-indigo-600' }}">
               Dashboard Home
            </a>
        </li>
        <li>
            <a href="{{ route('users.index') }}"
               class="{{ request()->routeIs('users.*') ? 'text-indigo-600 font-semibold' : 'text-gray-600 hover:text-indigo-600' }}">
               Users
            </a>
        </li>
        <li>
            <a href="{{ route('subscriptions.index') }}"
               class="{{ request()->routeIs('subscriptions.*') ? 'text-indigo-600 font-semibold' : 'text-gray-600 hover:text-indigo-600' }}">
               Subscription Lists
            </a>
        </li>
        <li>
            <a href="{{ route('bulk-emails.index') }}"
               class="{{ request()->routeIs('bulk-emails.*') ? 'text-indigo-600 font-semibold' : 'text-gray-600 hover:text-indigo-600' }}">
               Bulk Emails
            </a>
        </li>
    </ul>
</nav>