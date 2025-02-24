<!-- resources/views/users/create.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New User</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">

    @include('partials.header') <!-- If you have a shared header partial -->
    
    <div class="flex flex-grow">
        @include('partials.sidebar') <!-- If you have a shared sidebar partial -->

        <main class="flex-grow p-6">
            <a href="{{ url()->previous() }}"
                class="inline-block mb-4 bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-md transition">
                &larr; Back
            </a>
            <h2 class="text-2xl font-semibold mb-4">Add New User</h2>

            @if(session('status'))
                <div class="mb-4 text-green-500">{{ session('status') }}</div>
            @endif

            <!-- Display Validation Errors -->
            @if($errors->any())
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
                    <input name="name" type="text" placeholder="First Name" class="px-3 py-2 border rounded-md" required>
                    <input name="last_name" type="text" placeholder="Last Name" class="px-3 py-2 border rounded-md" required>
                </div>

                <input name="email" type="email" placeholder="Email" class="w-full px-3 py-2 border rounded-md mt-4" required>

                <div class="grid grid-cols-2 gap-4 mt-4">
                    <!-- user_type dropdown -->
<select name="user_type" class="px-3 py-2 border rounded-md" required>
    <option value="">Select User Type</option>
    @foreach($userTypes as $type)
        @php
            // We'll display the capitalized version, but the value remains lowercase
            $displayType = ucfirst($type);
            $valueType = strtolower($type);
        @endphp
        <option value="{{ $valueType }}">
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
        <option value="{{ $valueGender }}">
            {{ $displayGender }}
        </option>
    @endforeach
</select>
                </div>

                <input name="password" type="password" placeholder="Password (optional)" class="w-full px-3 py-2 border rounded-md mt-4">

                <button type="submit"
                    class="bg-indigo-500 text-white px-4 py-2 rounded-md mt-4 hover:bg-indigo-600 transition">
                    Create User
                </button>
            </form>
        </main>
    </div>
</body>
</html>