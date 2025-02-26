<!-- resources/views/partials/header.blade.php -->
<header class="bg-white shadow-md px-4 py-2 flex items-center w-full">
    
    <button 
            id="hamburgerBtn"
            class="md:hidden focus:outline-none"
        >
            <img src="{{ asset('assets/icons/hamburger.svg') }}" alt="Menu" class="w-6 h-6">
        </button>

    <!-- 2) Center portion for the logo -->
    <!-- flex-1 pushes it to occupy remaining space, so on mobile it's centered, on desktop it remains left if there's no hamburger -->
    <div class="flex-1 text-center md:text-left">
        <img 
            src="https://thatdisabilityadventurecompany.com.au/icons/logo.webp" 
            alt="TDAC Logo" 
            class="mx-auto md:mx-0 w-40"
        >
    </div>

    <!-- 3) Right portion: user icon and name -->
    @php
        $fullName = Auth::user()->name ?? 'Guest';
        $firstName = explode(' ', trim($fullName))[0] ?? 'Guest';
        $firstName = ucfirst(strtolower($firstName));
    @endphp

    <div class="relative ml-auto" x-data="{ openUser: false }" @click.away="openUser = false">
    <!-- Button toggling the dropdown -->
    <button
        @click="openUser = !openUser"
        class="flex items-center space-x-2 focus:outline-none"
    >
        <img src="{{ asset('assets/icons/user.svg') }}" alt="User Icon" class="w-5 h-5">

        <!-- On desktop, show the user name; on mobile, hide it -->
        <span class="hidden md:inline text-gray-800 font-medium">
            {{ $firstName ?? 'User' }}
        </span>

        <!-- optional arrow icons -->
        <svg x-show="!openUser" xmlns="http://www.w3.org/2000/svg"
             class="h-4 w-4 text-gray-600" fill="none"
             viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M5 9l7 7 7-7" />
        </svg>
        <svg x-show="openUser" xmlns="http://www.w3.org/2000/svg"
             class="h-4 w-4 text-gray-600" fill="none"
             viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M5 15l7-7 7 7" />
        </svg>
    </button>

    <!-- The user dropdown menu -->
    <div
        x-show="openUser"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 transform scale-95"
        x-transition:enter-end="opacity-100 transform scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 transform scale-100"
        x-transition:leave-end="opacity-0 transform scale-95"
        
        class="absolute top-full right-0 mt-2 w-40 bg-white border border-gray-200 shadow-md rounded py-2 z-50"
    >
        <!-- Profile Link -->
            <a 
                href="{{ route('profile.index') }}" 
                class="flex items-center space-x-2 px-3 py-2 hover:bg-gray-100 text-gray-700"
            >
                <img 
                    src="{{ asset('assets/icons/settings.svg') }}" 
                    alt="Settings Icon" 
                    class="w-4 h-4"
                >
                <span>My Profile</span>
            </a>

        <!-- Divider -->
        <div class="border-t border-gray-200 my-1"></div>

        <!-- Logout -->
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="w-full flex items-center space-x-2 px-3 py-2 hover:bg-gray-100 text-left text-gray-700">
                <img src="{{ asset('assets/icons/logout.svg') }}" alt="Logout" class="w-4 h-4">
                <span>Logout</span>
            </button>
        </form>
    </div>
</div>
</header>