@extends('layouts.app')

@section('title', 'Schedule Pages Settings')

@section('content')
    <h2 class="text-2xl font-semibold mb-4">Schedule Pages</h2>

    <!-- If you have any session status or error messages -->
    @if(session('status'))
        <div class="text-green-500 mb-4">{{ session('status') }}</div>
    @endif

    <!-- This is your settings content -->
    <p class="mb-4">
        Configure and manage your schedule pages here.
        (Replace with real content, forms, etc.)
    </p>

    <!-- Example: if you passed $pages from the controller
    @if(!empty($pages))
        <ul>
            @foreach($pages as $page)
                <li>{{ $page->title }}</li>
            @endforeach
        </ul>
    @endif
    -->

    <!-- Placeholder for your future form or content -->
    <div class="bg-white p-4 shadow rounded">
        <p>Example form or placeholder content.</p>
    </div>
@endsection