<!-- resources/views/account/home.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title>TDAC Account Home</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
<div class="max-w-md mx-auto mt-10 bg-white p-6 shadow-md rounded-md">
    <h2 class="text-xl font-semibold mb-4 text-center">Please Enter Your Name</h2>

    @if(session('error'))
        <p class="text-red-500 text-center">{{ session('error') }}</p>
    @endif

    <form method="POST" action="{{ route('profile.saveName') }}">
        @csrf
        <div class="mb-4">
            <input 
                name="name" 
                type="text" 
                placeholder="First Name" 
                class="w-full px-3 py-2 border rounded-md"
                required
            >
        </div>

        <div class="mb-4">
            <input 
                name="last_name" 
                type="text" 
                placeholder="Last Name" 
                class="w-full px-3 py-2 border rounded-md"
                required
            >
        </div>

        <button 
            type="submit"
            class="bg-indigo-500 text-white px-4 py-2 rounded-md w-full hover:bg-indigo-600 transition"
        >
            Save Name
        </button>
    </form>
</div>
</body>
</html>