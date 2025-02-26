@extends('layouts.app')

@section('title', 'Create New Email List')

@section('content')
<h2 class="text-2xl font-semibold mb-4">Create a New Email List</h2>

@if(session('status'))
    <div class="text-green-500 mb-4">{{ session('status') }}</div>
@endif

@if($errors->any())
    <div class="text-red-500 mb-4">
        <ul>
            @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="bg-white p-6 shadow-md rounded-md w-full mx-auto">
    <form method="POST" action="{{ route('subscriptions.store_list') }}">
        @csrf

        <!-- Friendly Name -->
        <div class="mb-6">
            <label for="friendly_name" class="block text-sm font-medium text-gray-700 mb-1">
                Friendly Name (e.g. "Calendar Release")
            </label>
            <input 
                type="text"
                name="friendly_name"
                id="friendly_name"
                class="w-full p-2 border rounded focus:outline-none focus:ring-indigo-500"
                required
                placeholder="Enter descriptive name"
            >

            <!-- Live preview of code-friendly name -->
            <div class="mt-1 text-sm text-gray-500">
                Code-friendly name: 
                <span id="codeNamePreview" class="font-mono text-gray-700"></span>
            </div>
        </div>

        <!-- Existing Lists Checkboxes -->
        <div class="mb-6">
            <h3 class="text-md font-semibold mb-2">Copy subscribers from existing lists:</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2">
                @forelse($existingLists as $list)
                    @php
                        $displayName = ucwords(str_replace('_',' ',$list->list_name));
                    @endphp
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="lists[]" value="{{ $list->list_name }}">
                        <span>{{ $displayName }}</span>
                    </label>
                @empty
                    <p class="col-span-3 text-gray-500">No existing lists found.</p>
                @endforelse
            </div>
            <p class="text-sm text-gray-500 mt-2">
                (Any subscribers in these lists will be automatically added to your new list.)
            </p>
        </div>

        <!-- Textarea for manual emails -->
        <div class="mb-6">
            <h3 class="text-md font-semibold mb-2">Add individual user emails (one per line):</h3>
            <textarea
                name="emails"
                rows="6"
                class="w-full p-2 border rounded focus:outline-none focus:ring-indigo-500"
                placeholder="e.g. jane@example.com"
            ></textarea>
            <p class="text-sm text-gray-500 mt-2">
                (We'll create or update user records automatically.)
            </p>
        </div>

        <button
            type="submit"
            class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 transition"
        >
            Create List
        </button>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const friendlyNameInput = document.getElementById('friendly_name');
    const codeNamePreview   = document.getElementById('codeNamePreview');

    function updateCodeNamePreview() {
        let val = friendlyNameInput.value.trim();
        // Transform to lowercase + hyphens:
        val = val.toLowerCase().replace(/\s+/g, '-');
        codeNamePreview.textContent = val;
    }

    friendlyNameInput.addEventListener('input', updateCodeNamePreview);
    updateCodeNamePreview(); // run once on load
});
</script>
@endpush