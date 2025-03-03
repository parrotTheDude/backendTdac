@extends('layouts.app')

@section('title', 'Add New User')

@section('content')
<a href="{{ url()->previous() }}"
   class="inline-block mb-4 bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-md transition">
   &larr; Back
</a>

<h2 class="text-2xl font-semibold mb-4">Add New User</h2>

<!-- Confirmation Box if newUserCreated is in the session -->
@if(session('newUserCreated'))
    @php
        $created = session('newUserCreated');
    @endphp
    <div id="successMessage" class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded mb-6" role="alert">
        <p class="font-bold">User Created Successfully!</p>
        <p class="mt-1">Name: {{ $created['name'] ?? 'N/A' }} {{ $created['last_name'] ?? '' }}</p>
        <p>Email: {{ $created['email'] }}</p>
    </div>

    <script>
        setTimeout(function() {
            document.getElementById('successMessage').style.display = 'none';
        }, 5000);
    </script>
@endif

@if(session('status'))
    <div class="mb-4 text-green-500">
        {{ session('status') }}
    </div>
@endif

@if($errors->any())
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            @foreach($errors->keys() as $errorField)
                document.querySelector("[name='{{ $errorField }}']").classList.add("border-red-500");
            @endforeach
        });
    </script>
    <div class="mb-4 text-red-500">
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('users.store') }}" class="bg-white p-6 shadow rounded-md">
    @csrf

    <div class="grid grid-cols-2 gap-4">
       <input 
            name="name" 
            type="text" 
            placeholder="First Name (Optional)" 
            class="px-3 py-2 border rounded-md"
            oninput="this.value = this.value.charAt(0).toUpperCase() + this.value.slice(1).toLowerCase();"
        >

        <input 
            name="last_name" 
            type="text" 
            placeholder="Last Name (Optional)" 
            class="px-3 py-2 border rounded-md"
            oninput="this.value = this.value.charAt(0).toUpperCase() + this.value.slice(1).toLowerCase();"
        >
    </div>

    <input 
        name="email" 
        type="email" 
        placeholder="Email" 
        class="w-full px-3 py-2 border rounded-md mt-4"
        required
    >

    <div class="grid grid-cols-2 gap-4 mt-4">
        <select name="user_type" class="px-3 py-2 border rounded-md" required>
            <option value="">Select User Type</option>
            @foreach($userTypes as $type)
                @php
                    $displayType = ucfirst($type);
                    $valueType   = strtolower($type);
                @endphp
                <option value="{{ $valueType }}">{{ $displayType }}</option>
            @endforeach
        </select>

        <select name="gender" class="px-3 py-2 border rounded-md">
            <option value="">Select Gender (optional)</option>
            @foreach($genders as $genderVal)
                @php
                    $displayGender = ucfirst($genderVal);
                    $valueGender   = strtolower($genderVal);
                @endphp
                <option value="{{ $valueGender }}">{{ $displayGender }}</option>
            @endforeach
        </select>
    </div>

    <!-- Subscription Lists -->
    <div class="mt-6">
        <h3 class="text-lg font-semibold mb-2">Subscription Lists</h3>
        @foreach($subscriptionLists as $list)
            <div class="flex items-center mb-2">
                <input type="checkbox" name="subscriptions[]" value="{{ $list }}" id="sub_{{ $list }}" 
                    class="mr-2">
                <label for="sub_{{ $list }}">{{ $list }}</label>
            </div>
        @endforeach
    </div>

    <button 
        type="submit"
        class="bg-indigo-500 text-white px-4 py-2 rounded-md mt-4 hover:bg-indigo-600 transition"
        onclick="this.disabled = true; this.innerText = 'Creating...'; this.form.submit();"
    >
        Create User
    </button>
</form>
@endsection
