@extends('layouts.app')

@section('title', ucfirst($listName) . ' Subscribers')

@section('content')
<h2 class="text-2xl font-semibold mb-4">
    Subscribers for {{ ucfirst($listName) }}
</h2>

<div class="flex items-center mb-4 space-x-2">
    <input type="text" id="searchInput" 
           class="px-3 py-2 border rounded-md shadow-sm focus:outline-none w-1/3"
           placeholder="Search subscribers...">
</div>

@if(session('status'))
    <div class="text-green-500 mb-4">{{ session('status') }}</div>
@endif

@if($subscribers->isEmpty())
    <p>No subscribers for this list.</p>
@else
    <table class="w-full bg-white shadow rounded-md overflow-hidden">
        <thead class="bg-gray-200">
            <tr>
                <th class="py-3 px-4 text-left">Name</th>
                <th class="py-3 px-4 text-left">Email</th>
                <th class="py-3 px-4 text-left">Actions</th>
            </tr>
        </thead>
        <tbody>
        @foreach($subscribers as $sub)
            <tr class="border-b hover:bg-gray-100">
                <td class="py-3 px-4">
                    {{ $sub->user->name ?? 'N/A' }} {{ $sub->user->last_name ?? '' }}
                </td>
                <td class="py-3 px-4">
                    {{ $sub->user->email ?? 'N/A' }}
                </td>
                <td class="py-3 px-4">
                    <!-- Unsubscribe form -->
                    <form method="POST" action="{{ route('subscriptions.unsubscribe') }}">
                        @csrf
                        <input type="hidden" name="email" value="{{ $sub->user->email }}">
                        <input type="hidden" name="list_name" value="{{ $listName }}">
                        <button class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">
                            Unsubscribe
                        </button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endif

<!-- Subscribe form -->
<div class="mt-6 bg-white p-4 rounded shadow">
    <h3 class="text-lg font-semibold mb-2">Subscribe a user to this list:</h3>

    <form method="POST" action="{{ route('subscriptions.subscribe') }}">
        @csrf
        <input type="hidden" name="list_name" value="{{ $listName }}">

        <label for="email" class="block text-sm font-medium text-gray-700">User Email:</label>
        <input type="email" name="email" class="w-full p-2 border rounded" required>

        <button class="bg-indigo-500 text-white px-4 py-2 rounded hover:bg-indigo-600 mt-2">
            Subscribe User
        </button>
    </form>
</div>

<script>
    document.getElementById('searchInput').addEventListener('input', function() {
        let filter = this.value.toLowerCase();
        document.querySelectorAll('tbody tr').forEach(row => {
            let name = row.children[0].innerText.toLowerCase();
            let email = row.children[1].innerText.toLowerCase();
            row.style.display = (name.includes(filter) || email.includes(filter)) ? '' : 'none';
        });
    });
</script>
@endsection