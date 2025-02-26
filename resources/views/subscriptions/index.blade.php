@extends('layouts.app')

@section('title', 'Subscriptions')

@section('content')
<h2 class="text-2xl font-semibold mb-4">Subscription Lists</h2>

@if(session('status'))
    <div class="mb-4 text-green-500">{{ session('status') }}</div>
@endif

<!-- Create & Delete Buttons -->
<div class="flex items-center mb-4 space-x-2">
    <a href="{{ route('subscriptions.create_list') }}"
       class="bg-indigo-500 text-white px-4 py-2 rounded hover:bg-indigo-600 transition">
        + Create New List
    </a>
</div>

@if($lists->isEmpty())
    <p>No subscription lists found.</p>
@else
    <table class="w-full bg-white shadow rounded-md overflow-hidden">
        <thead class="bg-gray-200">
            <tr>
                <th class="py-3 px-4 text-left">List Name</th>
                <th class="py-3 px-4 text-left">Subscribed</th>
                <th class="py-3 px-4 text-left">Unsubscribed</th>
                <th class="py-3 px-4 text-left">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($lists as $list)
                <tr class="border-b hover:bg-gray-100">
                    <td class="py-3 px-4">
                        @php
                            $formattedName = ucwords(str_replace('-', ' ', $list->list_name));
                        @endphp
                        {{ $formattedName }}
                    </td>
                    <td class="py-3 px-4">{{ $list->subscribed_count }}</td>
                    <td class="py-3 px-4">{{ $list->unsubscribed_count }}</td>
                    <td class="py-3 px-4">
                        <a href="{{ route('subscriptions.show', $list->list_name) }}"
                           class="text-indigo-600 hover:underline">
                            View Subscribers
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif
@endsection