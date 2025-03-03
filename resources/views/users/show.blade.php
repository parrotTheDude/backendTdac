@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
<!-- Back Button, Reset Password Button, and Resend Verification Email Button -->
<div class="flex flex-wrap items-center gap-2 mb-4">
    <a href="{{ url()->previous() }}"
       class="inline-block bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-md transition">
       &larr; Back
    </a>

    <form method="POST" action="{{ route('password.sendResetLink') }}">
        @csrf
        <input type="hidden" name="email" value="{{ $user->email }}">
        <button type="submit"
            class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-md transition">
            Reset Password
        </button>
    </form>

    @if(!$user->email_verified_at)
        <a href="{{ route('users.resendVerification', $user->id) }}"
           class="bg-indigo-500 text-white px-4 py-2 rounded-md hover:bg-indigo-600 transition">
           Resend Verification Email
        </a>
    @endif
</div>

<h2 class="text-2xl font-semibold mb-4">Edit User</h2>

<!-- Status Message (Success/Errors) -->
@if(session('status'))
    <div class="mb-4 text-green-500" aria-live="polite">
        {{ session('status') }}
    </div>
@endif

<form method="POST" action="{{ route('users.update', $user->id) }}" class="bg-white p-6 shadow rounded-md" id="editUserForm">
    @csrf
    @method('POST')

    <div class="grid grid-cols-2 gap-4">
        <input
            name="name"
            type="text"
            value="{{ $user->name }}"
            placeholder="First Name (Optional)"
            class="px-3 py-2 border rounded-md change-detect"
            oninput="this.value = this.value.charAt(0).toUpperCase() + this.value.slice(1).toLowerCase();"
        >

        <input
            name="last_name"
            type="text"
            value="{{ $user->last_name }}"
            placeholder="Last Name (Optional)"
            class="px-3 py-2 border rounded-md change-detect"
            oninput="this.value = this.value.charAt(0).toUpperCase() + this.value.slice(1).toLowerCase();"
        >
    </div>

    <input
        name="email"
        type="email"
        required
        value="{{ $user->email }}"
        placeholder="Email"
        class="w-full px-3 py-2 border rounded-md mt-4 change-detect"
    >

    <div class="grid grid-cols-2 gap-4 mt-4">
        <!-- user_type dropdown -->
        <select name="user_type" class="px-3 py-2 border rounded-md change-detect" required>
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
        <select name="gender" class="px-3 py-2 border rounded-md change-detect">
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

    <!-- Subscription Lists -->
    <div class="mt-6">
        <h3 class="text-lg font-semibold mb-2">Subscription Lists</h3>
        @foreach($subscriptionLists as $list)
            <div class="flex items-center mb-2">
                <input type="checkbox" name="subscriptions[]" value="{{ $list }}" id="sub_{{ $list }}" 
                    {{ in_array($list, $userSubscriptions) ? 'checked' : '' }}
                    class="mr-2 change-detect">
                <label for="sub_{{ $list }}">{{ $list }}</label>
            </div>
        @endforeach
    </div>

    <button
        type="submit"
        id="saveButton"
        class="bg-indigo-500 text-white px-4 py-2 rounded-md mt-4 hover:bg-indigo-600 transition opacity-50 cursor-not-allowed"
        disabled
    >
        Save Changes
    </button>
</form>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const form = document.getElementById("editUserForm");
        const saveButton = document.getElementById("saveButton");
        const inputs = document.querySelectorAll(".change-detect");

        function checkForChanges() {
            let hasChanges = false;
            inputs.forEach(input => {
                if (input.type === "checkbox") {
                    if (input.checked !== input.defaultChecked) hasChanges = true;
                } else {
                    if (input.value !== input.defaultValue) hasChanges = true;
                }
            });

            if (hasChanges) {
                saveButton.disabled = false;
                saveButton.classList.remove("opacity-50", "cursor-not-allowed");
            } else {
                saveButton.disabled = true;
                saveButton.classList.add("opacity-50", "cursor-not-allowed");
            }
        }

        inputs.forEach(input => {
            input.addEventListener("input", checkForChanges);
            input.addEventListener("change", checkForChanges);
        });

        checkForChanges();
    });
</script>
@endsection