<!-- resources/views/dashboard.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TDAC Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">

    @include('partials.header')
    <div class="flex flex-grow">
        @include('partials.sidebar')

        <!-- Main Content Area -->
        <main class="flex-grow p-6">
            <h2 class="text-2xl font-semibold mb-4">Welcome back!</h2>
            <p class="text-gray-600">Select an option from the sidebar to get started.</p>
        </main>

    </div>

</body>
</html>