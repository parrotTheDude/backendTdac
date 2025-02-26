<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel | TDAC Australia</title>
    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="//unpkg.com/alpinejs" defer></script>
</head>
<body class="bg-gray-100 min-h-screen">

    @include('partials.header')  <!-- Has the hamburger with ID="hamburgerBtn" -->

    <div class="flex">
        @include('partials.sidebar')  <!-- We'll define it below -->
        <main class="flex-grow p-4 pt-8">
            @yield('content')
        </main>
    </div>

    <!-- Basic script toggling the transform class on mobile -->
    <script>
      document.addEventListener('DOMContentLoaded', () => {
        const hamburger = document.getElementById('hamburgerBtn');
        const sidebar = document.getElementById('sidebarNav');

        // On mobile, toggling the '-translate-x-full' class shows/hides the sidebar.
        hamburger?.addEventListener('click', () => {
          sidebar.classList.toggle('-translate-x-full');
        });
      });
    </script>
</body>
</html>