<!-- resources/views/users/create.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New User - TDAC</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">

    @include('partials.header')
    
    <div class="flex flex-grow">
        @include('partials.sidebar')

        <main class="flex-grow p-6">
            <h2 class="text-2xl font-semibold mb-4">Add New User</h2>

            <a href="{{ url()->previous() }}"
               class="inline-block mb-4 bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-md transition">
                &larr; Back
            </a>

            @if($errors->any())
                <div class="mb-4 text-red-500">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('users.store') }}" class="bg-white p-6 shadow rounded-md">
                @csrf

                <div class="grid grid-cols-2 gap-4">
                    <input name="name" type="text" placeholder="First Name" class="px-3 py-2 border rounded-md">
                    <input name="last_name" type="text" placeholder="Last Name" class="px-3 py-2 border rounded-md">
                </div>

                <input name="email" type="email" required placeholder="Email"
                    class="w-full px-3 py-2 border rounded-md mt-4">

                <div class="grid grid-cols-2 gap-4 mt-4">
                    <input name="user_type" type="text" required placeholder="User Type"
                        class="px-3 py-2 border rounded-md">
                    <input name="gender" type="text" placeholder="Gender"
                        class="px-3 py-2 border rounded-md">
                </div>

                <input name="password" type="password" placeholder="Password"
                    class="w-full px-3 py-2 border rounded-md mt-4">

                <button type="submit"
                    class="bg-indigo-500 text-white px-4 py-2 rounded-md mt-4 hover:bg-indigo-600 transition">
                    Create User
                </button>
            </form>
        </main>
    </div>
</body>
</html>