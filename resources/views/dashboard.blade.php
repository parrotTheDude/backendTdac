<!-- resources/views/dashboard.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <title>TDAC Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex flex-col items-center justify-center h-screen gap-4">
    <h1 class="text-3xl font-bold">🎉 You're logged into TDAC!</h1>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded">
            Logout
        </button>
    </form>
</body>
</html>