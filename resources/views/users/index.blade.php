@extends('layouts.app')

@section('title', 'Users')

@section('content')
<div class="flex items-center mb-4">

    <!-- Left section: Magnifying glass + expanding search input -->
    <div class="flex items-center space-x-2">
        <!-- Magnifying Glass Button -->
        <button
            id="searchToggle"
            class="p-2 bg-gray-200 text-gray-600 rounded-full hover:bg-gray-300 focus:outline-none"
        >
            <img src="{{ asset('assets/icons/search.svg') }}" class="h-5 w-5" alt="Search Icon">
        </button>

        <!-- Expanding Search Container -->
        <div
            id="searchContainer"
            class="overflow-hidden transition-all duration-300
                   {{ request('search') ? 'w-64' : 'w-0' }}"
        >
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
        class="ml-auto bg-indigo-500 text-white px-4 py-2 rounded-md hover:bg-indigo-600 transition"
    >
        + Add New User
    </a>
</div>

<!-- Table with sortable headers -->
<table class="w-full bg-white shadow rounded-md overflow-hidden">
    <thead class="bg-gray-200">
        <tr>
            <th class="py-3 px-4 text-left">
                <a href="{{ route('users.index', array_merge(request()->all(), [
                    'sort' => 'name',
                    'direction' => ($sort === 'name' && $direction === 'asc') ? 'desc' : 'asc',
                ])) }}">
                    Name
                </a>
            </th>
            <th class="py-3 px-4 text-left">
                <a href="{{ route('users.index', array_merge(request()->all(), [
                    'sort' => 'last_name',
                    'direction' => ($sort === 'last_name' && $direction === 'asc') ? 'desc' : 'asc',
                ])) }}">
                    Last Name
                </a>
            </th>
            <th class="py-3 px-4 text-left">
                <a href="{{ route('users.index', array_merge(request()->all(), [
                    'sort' => 'email',
                    'direction' => ($sort === 'email' && $direction === 'asc') ? 'desc' : 'asc',
                ])) }}">
                    Email
                </a>
            </th>
            <th class="py-3 px-4 text-left">
                <a href="{{ route('users.index', array_merge(request()->all(), [
                    'sort' => 'user_type',
                    'direction' => ($sort === 'user_type' && $direction === 'asc') ? 'desc' : 'asc',
                ])) }}">
                    User Type
                </a>
            </th>
            <th class="py-3 px-4 text-left">
                <a href="{{ route('users.index', array_merge(request()->all(), [
                    'sort' => 'gender',
                    'direction' => ($sort === 'gender' && $direction === 'asc') ? 'desc' : 'asc',
                ])) }}">
                    Gender
                </a>
            </th>
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
@endsection

@push('scripts')
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
@endpush