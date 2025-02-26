@extends('layouts.app')

@section('title', 'My Profile Settings')

@section('content')
<h2 class="text-2xl font-semibold mb-4">My Profile</h2>

@if(session('status'))
    <div class="text-green-500 mb-4">
        {{ session('status') }}
    </div>
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

<!-- We'll do one form that handles all updates: name, email, notifications, plus password changes -->
<form action="{{ route('profile.update') }}" method="POST" class="bg-white p-4 shadow rounded max-w-xl">
    @csrf

    {{-- 1) Basic Info --}}
    <div class="mb-6">
        <h3 class="text-lg font-semibold mb-2">Basic Information</h3>

        <div class="mb-4">
            <label for="name" class="block text-sm font-medium text-gray-700">First Name</label>
            <input 
                type="text" 
                name="name" 
                id="name" 
                value="{{ old('name', $user->name) }}"
                class="mt-1 block w-full px-3 py-2 border rounded-md shadow-sm 
                       focus:outline-none focus:ring-indigo-500"
            >
        </div>

        <div class="mb-4">
            <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name</label>
            <input 
                type="text" 
                name="last_name" 
                id="last_name"
                value="{{ old('last_name', $user->last_name) }}"
                class="mt-1 block w-full px-3 py-2 border rounded-md shadow-sm 
                       focus:outline-none focus:ring-indigo-500"
            >
        </div>

        <!-- Email + Verification Status -->
        <div class="mb-4">
            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
            <div class="mt-1 flex items-center space-x-2">
                <input 
                    type="email" 
                    name="email" 
                    id="email" 
                    value="{{ old('email', $user->email) }}" 
                    required
                    class="block w-full px-3 py-2 border rounded-md shadow-sm 
                           focus:outline-none focus:ring-indigo-500"
                >

                <!-- Check if user is verified or not -->
                @if($user->email_verified_at)
                    <!-- verified -->
                    <img 
                        src="{{ asset('assets/icons/correct.svg') }}" 
                        alt="Verified" 
                        class="w-5 h-5"
                        title="Email Verified"
                    >
                @else
                    <!-- not verified -->
                    <img 
                        src="{{ asset('assets/icons/remove.svg') }}" 
                        alt="Not Verified" 
                        class="w-5 h-5"
                        title="Email Not Verified"
                    >

                    <!-- "Verify Now" link if you want to let user or admin manually trigger resend -->
                    <a href="{{ route('users.resendVerification', $user->id) }}"
                       class="text-xs text-indigo-600 hover:text-indigo-800"
                       title="Send verification email again">
                        Verify Now
                    </a>
                @endif
            </div>
        </div>
    </div>

    {{-- 2) Notification Preferences --}}
    <div class="mb-6">
        <h3 class="text-lg font-semibold mb-2">Notification Preferences</h3>
        
        <!-- Example checkboxes. Adapt to your real preferences. -->
        <div class="flex items-center mb-2">
            <input 
                type="checkbox" 
                name="notify_newsletter" 
                id="notify_newsletter" 
                value="1"
                @if(old('notify_newsletter', $user->notify_newsletter)) checked @endif
                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
            >
            <label for="notify_newsletter" class="ml-2 text-sm text-gray-700">
                Subscribe to monthly newsletter
            </label>
        </div>

        <div class="flex items-center mb-2">
            <input 
                type="checkbox" 
                name="notify_promotions" 
                id="notify_promotions"
                value="1"
                @if(old('notify_promotions', $user->notify_promotions)) checked @endif
                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
            >
            <label for="notify_promotions" class="ml-2 text-sm text-gray-700">
                Send me promotional offers
            </label>
        </div>

        <div class="flex items-center mb-2">
            <input 
                type="checkbox" 
                name="notify_updates" 
                id="notify_updates"
                value="1"
                @if(old('notify_updates', $user->notify_updates)) checked @endif
                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
            >
            <label for="notify_updates" class="ml-2 text-sm text-gray-700">
                Receive app updates & alerts
            </label>
        </div>
    </div>

    {{-- 3) Change Password --}}
    <div class="mb-6">
        <h3 class="text-lg font-semibold mb-2">Change Password</h3>

        <!-- Current password for security (optional) -->
        <div class="mb-4">
            <label for="current_password" class="block text-sm font-medium text-gray-700">Current Password</label>
            <input 
                type="password" 
                name="current_password" 
                id="current_password"
                class="mt-1 block w-full px-3 py-2 border rounded-md shadow-sm 
                       focus:outline-none focus:ring-indigo-500"
            >
        </div>

        <div class="mb-4">
            <label for="new_password" class="block text-sm font-medium text-gray-700">New Password</label>
            <input
                type="password"
                name="new_password"
                id="new_password"
                class="mt-1 block w-full px-3 py-2 border rounded-md shadow-sm 
                       focus:outline-none focus:ring-indigo-500"
            >
        </div>

        <div class="mb-4">
            <label for="new_password_confirmation" class="block text-sm font-medium text-gray-700">Confirm New Password</label>
            <input
                type="password"
                name="new_password_confirmation"
                id="new_password_confirmation"
                class="mt-1 block w-full px-3 py-2 border rounded-md shadow-sm 
                       focus:outline-none focus:ring-indigo-500"
            >
        </div>
    </div>

    <button 
        type="submit"
        class="bg-indigo-600 text-white py-2 px-4 rounded hover:bg-indigo-700 transition"
    >
        Save Changes
    </button>
</form>
@endsection