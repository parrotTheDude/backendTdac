<!-- resources/views/layouts/auth.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Accounts') | TDAC Australia</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen px-4">
    <!-- Wrapper for the form or content -->
    <div class="bg-white p-6 sm:p-8 rounded-lg shadow-md w-full max-w-md mx-auto">
        @yield('content')
    </div>

    @stack('scripts')
</body>
</html>