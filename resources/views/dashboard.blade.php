@extends('layouts.app')

@section('content')
@php
    $user = Auth::user();
    // Check if either field is empty
    $isIncomplete = empty($user->name) || empty($user->last_name);
@endphp

<h2 class="text-2xl font-semibold mb-4">
    Welcome, {{ $user->name ?: 'Friend' }}!
</h2>
<p class="text-gray-600">
    Let's get started! Choose an item on the sidebar to jump in.
</p>

<!-- Alpine-driven modal for incomplete name/last_name -->
<div x-data="{ showModal: {{ $isIncomplete ? 'true' : 'false' }} }">
    <template x-if="showModal">
        <!-- Dark overlay & container -->
        <div 
            class="fixed inset-0 flex items-center justify-center z-50"
            style="background-color: rgba(0, 0, 0, 0.5);"
        >
            <!-- Modal box -->
            <div 
                class="bg-white p-6 rounded shadow-md w-96 relative"
                @click.away="showModal = false"
            >
                <h3 class="text-xl font-semibold mb-4">
                    Please tell us your name
                </h3>

                <form action="{{ route('profile.updateName') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700" for="name">
                            First Name
                        </label>
                        <input 
                            type="text" 
                            name="name" 
                            id="name"
                            class="mt-1 p-2 border w-full rounded"
                            value="{{ old('name', $user->name) }}"
                            required
                        >
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700" for="last_name">
                            Last Name
                        </label>
                        <input 
                            type="text"
                            name="last_name"
                            id="last_name"
                            class="mt-1 p-2 border w-full rounded"
                            value="{{ old('last_name', $user->last_name) }}"
                            required
                        >
                    </div>

                    <div class="flex justify-end space-x-2">
                        <button 
                            type="button"
                            class="px-4 py-2 text-gray-700 border rounded hover:bg-gray-100"
                            @click="showModal = false"
                        >
                            Cancel
                        </button>
                        <button 
                            type="submit"
                            class="px-4 py-2 bg-indigo-500 text-white rounded hover:bg-indigo-600"
                        >
                            Save
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </template>
</div>
@endsection