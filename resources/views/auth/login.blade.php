@extends('layouts.auth')

@section('title', 'Login')

@section('content')

<!-- TDAC Logo -->
<div class="mb-4 flex justify-center">
    <img src="https://thatdisabilityadventurecompany.com.au/icons/logo.webp"
         alt="TDAC Logo"
         class="w-48 sm:w-48 h-auto">
</div>

<h1 class="text-l sm:text-2xl font-semibold text-center mb-6">Login</h1>

@if(session('status'))
    <div class="text-green-500 mb-4 text-center">{{ session('status') }}</div>
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

<form method="POST" action="{{ route('login.submit') }}">
    @csrf

    <!-- Email -->
    <div class="mb-4">
        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
        <input 
            type="email" 
            id="email" 
            name="email" 
            required 
            autofocus
            class="mt-1 block w-full px-3 py-2 border rounded-md shadow-sm 
                   focus:outline-none focus:ring-indigo-500"
        >
    </div>

    <!-- Password + Toggle Eye -->
    <div class="mb-4 relative">
        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
        <input 
            type="password" 
            id="password" 
            name="password" 
            required
            class="mt-1 block w-full px-3 py-2 border rounded-md shadow-sm 
                   pr-10 focus:outline-none focus:ring-indigo-500"
        >
        <!-- Eye toggle button -->
        <button 
            type="button" 
            class="absolute right-3 top-9 text-gray-500"
            aria-label="Show or hide password"
            onclick="togglePassword()"
        >
            <!-- default to eyebrow.svg (hidden password) -->
            <img 
                id="passwordIcon" 
                src="{{ asset('assets/icons/eyebrow.svg') }}" 
                alt="Show password" 
                class="w-4 h-4"
            >
        </button>
    </div>

    <!-- Row: Remember Me & Forgot Password? -->
    <div class="flex items-center justify-between mb-6">
        <!-- Remember me -->
        <label class="flex items-center text-sm text-gray-700 space-x-2">
            <input 
                type="checkbox" 
                name="remember" 
                id="remember"
                class="form-checkbox h-4 w-4 text-indigo-600"
            >
            <span>Remember me</span>
        </label>
        
        <!-- Forgot password link -->
        <a 
            href="{{ route('password.request') }}" 
            class="text-sm text-gray-500 hover:text-gray-700"
        >
            Forgot password?
        </a>
    </div>

    <!-- Submit Button -->
    <button 
        type="submit"
        class="w-full bg-indigo-600 text-white py-2 rounded-md hover:bg-indigo-700 transition"
    >
        Login
    </button>
</form>

@push('scripts')
<script>
function togglePassword() {
    const passField = document.getElementById('password');
    const iconField = document.getElementById('passwordIcon');

    if (passField.type === 'password') {
        passField.type = 'text';
        // Switch icon to "eye-close-up.svg"
        iconField.src = '{{ asset('assets/icons/eye-close-up.svg') }}';
        iconField.alt = 'Hide password';
    } else {
        passField.type = 'password';
        // Switch icon back to "eyebrow.svg"
        iconField.src = '{{ asset('assets/icons/eyebrow.svg') }}';
        iconField.alt = 'Show password';
    }
}
</script>
@endpush

@endsection