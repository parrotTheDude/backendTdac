@extends('layouts.auth')

@section('title', 'Forgot Password')

@section('content')

<!-- Back to Login Link -->
<div class="mb-2 text-center">
    <a href="{{ route('login') }}" class="text-sm text-gray-500 hover:text-gray-700">
        ← Back to Login
    </a>
</div>

<!-- TDAC Logo -->
<div class="mb-4 flex justify-center">
    <img 
        src="https://thatdisabilityadventurecompany.com.au/icons/logo.webp"
        alt="TDAC Logo"
        class="w-48 h-auto"
    >
</div>

<h2 class="text-xl sm:text-2xl font-semibold text-center mb-6">
    Forgot Password
</h2>

@if(session('status'))
    <div class="text-green-500 mb-4 text-center">
        {{ session('status') }}
    </div>
@endif

@if($errors->any())
    <div class="text-red-500 mb-4 text-center">
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('password.email') }}">
    @csrf

    <div class="mb-4">
        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
            Enter Your Email
        </label>
        <input 
            id="email"
            name="email" 
            type="email" 
            required 
            placeholder="you@example.com"
            class="w-full px-3 py-2 border rounded-md shadow-sm 
                   focus:outline-none focus:ring-indigo-500"
        >
    </div>

    <button 
        type="submit"
        class="w-full bg-indigo-600 text-white py-2 rounded-md 
               hover:bg-indigo-700 transition"
    >
        Send Reset Link
    </button>
</form>
@endsection