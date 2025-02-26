@extends('layouts.app')

@section('title', 'Bookings')

@section('content')
<h2 class="text-2xl font-semibold mb-6">Bookings</h2>

@if(session('status'))
    <div class="text-green-500 mb-4">
        {{ session('status') }}
    </div>
@endif

<p class="mb-4">
    This is your Bookings page. You could list or manage bookings here.
</p>

<!-- Example if you passed $bookings from the controller:
@isset($bookings)
    <ul>
        @foreach($bookings as $booking)
            <li>Booking #{{ $booking->id }} for {{ $booking->customer_name }}</li>
        @endforeach
    </ul>
@endisset
-->

@endsection