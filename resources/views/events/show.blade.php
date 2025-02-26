@if(session('status'))
    <div class="bg-green-100 text-green-600 p-3 rounded">
        {{ session('status') }}
    </div>
@endif

<h1>{{ $event->title }}</h1>
<p>{{ $event->details }}</p>
<!-- etc. -->

@auth
<form method="POST" action="{{ route('bookings.store', $event) }}">
    @csrf
    <!-- If you have fields like 'pickup_location' or 'medication': -->
    <!-- <input type="text" name="pickup_location" placeholder="Pickup location" /> -->
    <button type="submit" class="bg-indigo-500 text-white px-4 py-2 rounded">
        Book This Event
    </button>
</form>
@endauth