<!-- resources/views/subscriptions/index.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Events</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* For the sliding effect (translate-x-full means fully off-screen to the right) */
        .transition-transform {
            transition: transform 0.3s ease;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">

    @include('partials.header')

    <div class="flex flex-grow">
        @include('partials.sidebar')

    <main class="flex-grow p-6">
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-2xl font-bold">Events</h1>
            
            <!-- If the user is admin/master, show "Create Event" button -->
            @can('master') 
            <!-- or whatever gate/middleware you use for admin -->
            <a href="{{ route('events.create') }}"
               class="bg-indigo-500 text-white px-4 py-2 rounded hover:bg-indigo-600">
               + Create New Event
            </a>
            @endcan
        </div>

        <!-- Display any status messages -->
        @if(session('status'))
            <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
                {{ session('status') }}
            </div>
        @endif

        @if($events->isEmpty())
            <p>No events found.</p>
        @else
            <table class="w-full bg-white shadow rounded">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="text-left py-3 px-4">Title</th>
                        <th class="text-left py-3 px-4">Start Time</th>
                        <th class="text-left py-3 px-4">End Time</th>
                        <th class="text-left py-3 px-4">Location</th>
                        <th class="text-left py-3 px-4"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($events as $event)
                    <tr class="border-b hover:bg-gray-100">
                        <td class="py-3 px-4">
                            {{ $event->title }}
                        </td>
                        <td class="py-3 px-4">
                            {{ $event->start_time->format('d M Y, h:ia') }}
                        </td>
                        <td class="py-3 px-4">
                            @if($event->end_time)
                                {{ $event->end_time->format('d M Y, h:ia') }}
                            @else
                                --
                            @endif
                        </td>
                        <td class="py-3 px-4">
                            {{ $event->location ?? '--' }}
                        </td>
                        <td class="py-3 px-4">
                            <!-- Link to show or edit the event -->
                            <a href="{{ route('events.show', $event) }}"
                               class="text-indigo-600 hover:underline">
                               View
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </main>
</body>
</html>