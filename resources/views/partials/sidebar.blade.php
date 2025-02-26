<!-- resources/views/partials/sidebar.blade.php -->
<nav
    id="sidebarNav"
    class="
      fixed md:static
      md:w-56 w-56
      h-screen bg-white shadow-md p-4 pt-8 overflow-y-auto
      transform transition-transform duration-300 ease-out
      -translate-x-full md:translate-x-0
      z-50
    "
>
    <ul class="space-y-2 list-none">
        <!-- 1) Dashboard -->
        <li>
            <a href="{{ route('dashboard') }}"
               class="flex items-center space-x-2 px-2 py-2 rounded 
               {{ request()->routeIs('dashboard') ? 'bg-indigo-100 text-indigo-600' : 'text-gray-700 hover:bg-gray-100' }}">
                <img src="{{ asset('assets/icons/home.svg') }}" alt="Home Icon" class="w-5 h-5">
                <span>Dashboard</span>
            </a>
        </li>

        <!-- 2) Events (Dropdown) -->
        <li x-data="{ open: false }" class="relative">
            <!-- Trigger button -->
            <button
                @click="open = !open"
                class="w-full flex items-center justify-between space-x-2 px-2 py-2 rounded
                text-left focus:outline-none
                {{ request()->routeIs('events.*') ? 'bg-indigo-100 text-indigo-600' : 'text-gray-700 hover:bg-gray-100' }}"
            >
                <div class="flex items-center space-x-2">
                    <img src="{{ asset('assets/icons/calendar.svg') }}" alt="Calendar Icon" class="w-5 h-5">
                    <span>Events</span>
                </div>
                <!-- Chevron -->
                <svg x-show="!open" xmlns="http://www.w3.org/2000/svg"
                     class="h-4 w-4 text-gray-600" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round"
                        d="M5 9l7 7 7-7" />
                </svg>
                <svg x-show="open" xmlns="http://www.w3.org/2000/svg"
                     class="h-4 w-4 text-gray-600" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round"
                        d="M5 15l7-7 7 7" />
                </svg>
            </button>

            <!-- Dropdown content -->
            <div
                x-show="open"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 transform scale-95"
                x-transition:enter-end="opacity-100 transform scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 transform scale-100"
                x-transition:leave-end="opacity-0 transform scale-95"
                class="mt-1 ml-6 space-y-1"
                @click.away="open = false"
            >
                <!-- Example sub-links -->
                <a href="{{ route('events.index') }}"
                   class="block px-2 py-2 rounded
                   {{ request()->routeIs('events.index') ? 'bg-indigo-100 text-indigo-600' : 'text-gray-700 hover:bg-gray-100' }}">
                   All Events
                </a>
                <a href="#" class="block px-2 py-2 rounded text-gray-700 hover:bg-gray-100">
                   January Schedules
                </a>
                <a href="#" class="block px-2 py-2 rounded text-gray-700 hover:bg-gray-100">
                   February Schedules
                </a>
                <a href="#" class="block px-2 py-2 rounded text-gray-700 hover:bg-gray-100">
                   March Schedules
                </a>
            </div>
        </li>

        <!-- 3) Bookings -->
<li>
    <a href="{{ route('bookings.index') }}"
       class="flex items-center space-x-2 px-2 py-2 rounded
              {{ request()->routeIs('bookings.*') ? 'bg-indigo-100 text-indigo-600' : 'text-gray-700 hover:bg-gray-100' }}">
        <img src="{{ asset('assets/icons/ticket.svg') }}" alt="Ticket Icon" class="w-5 h-5">
        <span>Bookings</span>
    </a>
</li>

        <!-- 4) Users -->
        <li>
            <a href="{{ route('users.index') }}"
               class="flex items-center space-x-2 px-2 py-2 rounded
               {{ request()->routeIs('users.*') ? 'bg-indigo-100 text-indigo-600' : 'text-gray-700 hover:bg-gray-100' }}">
                <img src="{{ asset('assets/icons/team.svg') }}" alt="Team Icon" class="w-5 h-5">
                <span>Users</span>
            </a>
        </li>

        <!-- 5) Emails (Dropdown) -->
        <li x-data="{ open: false }" class="relative">
            <!-- Trigger button -->
            <button @click="open = !open"
                    class="w-full flex items-center justify-between space-x-2 px-2 py-2 rounded
                    text-left focus:outline-none
                    {{ (request()->routeIs('subscriptions.*') || request()->routeIs('bulk-emails.*')) ? 'bg-indigo-100 text-indigo-600' : 'text-gray-700 hover:bg-gray-100' }}">
                <div class="flex items-center space-x-2">
                    <img src="{{ asset('assets/icons/email.svg') }}" alt="Email Icon" class="w-5 h-5">
                    <span>Emails</span>
                </div>
                <!-- Chevron -->
                <svg x-show="!open" xmlns="http://www.w3.org/2000/svg"
                     class="h-4 w-4 text-gray-600" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round"
                        d="M5 9l7 7 7-7" />
                </svg>
                <svg x-show="open" xmlns="http://www.w3.org/2000/svg"
                     class="h-4 w-4 text-gray-600" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round"
                        d="M5 15l7-7 7 7" />
                </svg>
            </button>
            <!-- Sub-links -->
            <div x-show="open"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 transform scale-95"
                 x-transition:enter-end="opacity-100 transform scale-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 transform scale-100"
                 x-transition:leave-end="opacity-0 transform scale-95"
                 class="mt-1 ml-6 space-y-1"
                 @click.away="open = false"
            >
                <a href="{{ route('subscriptions.index') }}"
                   class="block px-2 py-2 rounded
                   {{ request()->routeIs('subscriptions.*') ? 'bg-indigo-100 text-indigo-600' : 'text-gray-700 hover:bg-gray-100' }}">
                   Subscriptions
                </a>
                <a href="{{ route('bulk-emails.index') }}"
                   class="block px-2 py-2 rounded
                   {{ request()->routeIs('bulk-emails.*') ? 'bg-indigo-100 text-indigo-600' : 'text-gray-700 hover:bg-gray-100' }}">
                   Bulk Emails
                </a>
            </div>
        </li>

        <!-- 6) Settings -->
        <li x-data="{ open: false }" class="relative">
            <!-- The button that toggles sub-links -->
            <button @click="open = !open"
                    class="w-full flex items-center justify-between space-x-2 px-2 py-2 rounded
                           text-left focus:outline-none
                           {{ request()->routeIs('settings.*') ? 'bg-indigo-100 text-indigo-600' : 'text-gray-700 hover:bg-gray-100' }}">
                <div class="flex items-center space-x-2">
                    <img src="{{ asset('assets/icons/settings.svg') }}" alt="Settings Icon" class="w-5 h-5">
                    <span>Settings</span>
                </div>

                <!-- Chevrons for open/close if desired -->
                <svg x-show="!open" xmlns="http://www.w3.org/2000/svg" 
                     class="h-4 w-4 text-gray-600" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round"
                        d="M5 9l7 7 7-7" />
                </svg>
                <svg x-show="open" xmlns="http://www.w3.org/2000/svg"
                     class="h-4 w-4 text-gray-600" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round"
                        d="M5 15l7-7 7 7" />
                </svg>
            </button>

            <!-- Dropdown content -->
            <div x-show="open"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 transform scale-95"
                 x-transition:enter-end="opacity-100 transform scale-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 transform scale-100"
                 x-transition:leave-end="opacity-0 transform scale-95"
                 class="mt-1 ml-6 space-y-1"
                 @click.away="open = false"
            >
                <!-- Schedule Pages Link -->
                <a href="{{ route('settings.schedulePages') }}"
                   class="block px-2 py-2 rounded
                          {{ request()->routeIs('settings.schedulePages') ? 'bg-indigo-100 text-indigo-600' : 'text-gray-700 hover:bg-gray-100' }}">
                    Schedule Pages
                </a>
            </div>
        </li>

    </ul>
</nav>