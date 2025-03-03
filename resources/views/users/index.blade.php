@extends('layouts.app')

@section('title', 'Users')

@section('content')
<div class="flex items-center mb-4">
    <!-- Left section: Filter Buttons + Search input -->
    <div class="flex items-center space-x-2">
        <!-- Participants Filter Button -->
        <button
            onclick="filterUsers('participants')"
            class="p-2 {{ request('filter') === 'participants' ? 'bg-blue-700' : 'bg-blue-500' }} text-white rounded-md hover:bg-blue-600 transition">
            Participants
        </button>

        <!-- External Filter Button -->
        <button
            onclick="filterUsers('external')"
            class="p-2 {{ request('filter') === 'external' ? 'bg-green-700' : 'bg-green-500' }} text-white rounded-md hover:bg-green-600 transition">
            External
        </button>

        <!-- Staff Filter Button -->
        <button
            onclick="filterUsers('staff')"
            class="p-2 {{ request('filter') === 'staff' ? 'bg-red-700' : 'bg-red-500' }} text-white rounded-md hover:bg-red-600 transition">
            Staff
        </button>

        <!-- Reset Filters Button -->
        <button
            onclick="resetFilters()"
            class="p-2 bg-gray-400 text-white rounded-md hover:bg-gray-500 transition">
            Reset Filters
        </button>

        <a
        href="{{ route('users.create') }}"
        class="ml-auto bg-indigo-500 text-white px-4 py-2 rounded-md hover:bg-indigo-600 transition">
        + Add New User
    </a>

        <!-- Download CSV Button -->
        <a href="{{ route('users.export', request()->all()) }}" 
           class="p-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition">
           Download CSV
        </a>
    </div>
</div>

<!-- Search Box -->
<div class="mb-4">
    <form method="GET" action="{{ route('users.index') }}" class="w-full">
        <input
            type="text"
            id="searchInput"
            name="search"
            placeholder="Search..."
            value="{{ request('search') }}"
            class="p-2 border rounded-md w-full shadow-sm focus:outline-none"
            onkeyup="filterTable()"
        >
    </form>
</div>

<!-- Table with sortable headers -->
<table class="w-full bg-white shadow rounded-md overflow-hidden">
    <thead class="bg-gray-200">
        <tr>
            <th class="py-3 px-4 text-left">Name</th>
            <th class="py-3 px-4 text-left">Last Name</th>
            <th class="py-3 px-4 text-left">Email</th>
            <th class="py-3 px-4 text-left">User Type</th>
            <th class="py-3 px-4 text-left">Gender</th>
        </tr>
    </thead>
    <tbody id="userTable">
    @foreach($users as $index => $user)
        <tr class="border-b hover:bg-gray-100 user-row" style="display: {{ $index < 25 ? 'table-row' : 'none' }}">
            <td class="py-3 px-4">{{ $user->name }}</td>
            <td class="py-3 px-4">{{ $user->last_name }}</td>
            <td class="py-3 px-4">
                <a href="{{ route('users.show', $user->id) }}" class="text-indigo-600 hover:underline">
                    {{ $user->email }}
                </a>
            </td>
            <td class="py-3 px-4">
                <span class="px-2 py-1 rounded text-white 
                    {{ $user->user_type === 'admin' || $user->user_type === 'superadmin' ? 'bg-red-500' : '' }}
                    {{ $user->user_type === 'participant' || $user->user_type === 'parent' ? 'bg-blue-500' : '' }}
                    {{ $user->user_type === 'external' ? 'bg-green-500' : '' }}">
                    {{ ucfirst($user->user_type) }}
                </span>
            </td>
            <td class="py-3 px-4">{{ $user->gender }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<!-- Show More Button (Hidden if less than 25 users) -->
@if($users->count() > 25)
<div class="flex justify-center mt-4">
    <button id="showMoreBtn" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 transition" onclick="showMoreUsers()">
        Show More
    </button>
</div>
@endif
@endsection

@push('scripts')
<script>
    let visibleRows = 25;

    function filterUsers(type) {
        window.location.href = "{{ route('users.index') }}" + "?filter=" + type;
    }

    function resetFilters() {
        window.location.href = "{{ route('users.index') }}";
    }

    function showMoreUsers() {
        let rows = document.querySelectorAll('.user-row');
        let newVisibleRows = visibleRows + 25;

        for (let i = visibleRows; i < newVisibleRows && i < rows.length; i++) {
            rows[i].style.display = 'table-row';
        }

        visibleRows = newVisibleRows;

        if (visibleRows >= rows.length) {
            document.getElementById('showMoreBtn').style.display = 'none';
        }
    }
    function filterTable() {
        let input = document.getElementById("searchInput").value.toLowerCase();
        let rows = document.querySelectorAll("#userTable tr");
        
        rows.forEach(row => {
            let name = row.children[0].innerText.toLowerCase();
            let lastName = row.children[1].innerText.toLowerCase();
            let email = row.children[2].innerText.toLowerCase();
            
            if (name.includes(input) || lastName.includes(input) || email.includes(input)) {
                row.style.display = "table-row";
            } else {
                row.style.display = "none";
            }
        });
    }
</script>
@endpush
