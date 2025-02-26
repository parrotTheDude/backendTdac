@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
<!-- Back Button -->
<a href="{{ url()->previous() }}"
   class="inline-block mb-4 bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-md transition">
   &larr; Back
</a>

<h2 class="text-2xl font-semibold mb-4">Edit User</h2>

<!-- Optional: Resend Verification Email if not verified -->
@if(!$user->email_verified_at)
    <a href="{{ route('users.resendVerification', $user->id) }}"
       class="bg-indigo-500 text-white px-4 py-2 rounded-md hover:bg-indigo-600 transition mb-4 inline-block">
       Resend Verification Email
    </a>
@endif

<!-- Status Message (Success/Errors) -->
@if(session('status'))
    <div class="mb-4 text-green-500">
        {{ session('status') }}
    </div>
@endif

<form method="POST" action="{{ route('users.update', $user->id) }}" class="bg-white p-6 shadow rounded-md">
    @csrf

    <div class="grid grid-cols-2 gap-4">
        <input
            name="name"
            type="text"
            value="{{ $user->name }}"
            placeholder="First Name (Optional)"
            class="px-3 py-2 border rounded-md"
        >

        <input
            name="last_name"
            type="text"
            value="{{ $user->last_name }}"
            placeholder="Last Name (Optional)"
            class="px-3 py-2 border rounded-md"
        >
    </div>

    <input
        name="email"
        type="email"
        required
        value="{{ $user->email }}"
        placeholder="Email"
        class="w-full px-3 py-2 border rounded-md mt-4"
    >

    <div class="grid grid-cols-2 gap-4 mt-4">
        <!-- user_type dropdown -->
        <select name="user_type" class="px-3 py-2 border rounded-md" required>
            <option value="">Select User Type</option>
            @foreach($userTypes as $type)
                @php
                    $displayType = ucfirst($type);
                    $valueType   = strtolower($type);
                @endphp
                <option
                    value="{{ $valueType }}"
                    @if(strtolower($user->user_type) === $valueType) selected @endif
                >
                    {{ $displayType }}
                </option>
            @endforeach
        </select>

        <!-- gender dropdown -->
        <select name="gender" class="px-3 py-2 border rounded-md">
            <option value="">Select Gender (optional)</option>
            @foreach($genders as $genderVal)
                @php
                    $displayGender = ucfirst($genderVal);
                    $valueGender   = strtolower($genderVal);
                @endphp
                <option
                    value="{{ $valueGender }}"
                    @if(strtolower($user->gender) === $valueGender) selected @endif
                >
                    {{ $displayGender }}
                </option>
            @endforeach
        </select>
    </div>

    <input
        name="password"
        type="password"
        placeholder="New Password (leave blank to keep current)"
        class="w-full px-3 py-2 border rounded-md mt-4"
    >

    <button
        type="submit"
        class="bg-indigo-500 text-white px-4 py-2 rounded-md mt-4 hover:bg-indigo-600 transition"
    >
        Save Changes
    </button>
</form>
@endsection