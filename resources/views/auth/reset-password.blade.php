@extends('layouts.auth')

@section('title', 'Reset Password')

@section('content')
<!-- TDAC Logo -->
<div class="mb-4 flex justify-center">
    <img 
        src="https://thatdisabilityadventurecompany.com.au/icons/logo.webp" 
        alt="TDAC Logo" 
        class="w-48 h-auto"
    >
</div>

<!-- Short Instruction -->
<p class="text-sm text-gray-600 mb-6 text-center">
    Enter and confirm your new password below.
</p>

@if(session('status'))
    <div class="text-green-500 mb-4 text-center">
        {{ session('status') }}
    </div>
@endif

@if($errors->any())
    <div class="text-red-500 mb-4 text-center">
        <ul>
            @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('password.update') }}">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">
    <input type="hidden" name="email" value="{{ request('email') }}">

    <!-- New Password (show/hide toggle) -->
    <div class="mb-4 relative">
        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
            New Password
        </label>
        <input
            id="password"
            name="password"
            type="password"
            required
            class="w-full px-3 py-2 border rounded-md shadow-sm 
                   pr-10 focus:outline-none focus:ring-indigo-500"
        >
        <button
            type="button"
            class="absolute right-3 top-9 text-gray-500"
            aria-label="Show or hide password"
            onclick="togglePassword('password', 'passwordIcon')"
        >
            <img
                id="passwordIcon"
                src="{{ asset('assets/icons/eyebrow.svg') }}"
                alt="Show password"
                class="w-4 h-4"
            >
        </button>
    </div>

    <!-- Confirm Password (show/hide toggle) -->
    <div class="mb-4 relative">
        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
            Confirm Password
        </label>
        <input
            id="password_confirmation"
            name="password_confirmation"
            type="password"
            required
            class="w-full px-3 py-2 border rounded-md shadow-sm 
                   pr-10 focus:outline-none focus:ring-indigo-500"
        >
        <button
            type="button"
            class="absolute right-3 top-9 text-gray-500"
            aria-label="Show or hide password"
            onclick="togglePassword('password_confirmation', 'confirmIcon')"
        >
            <img
                id="confirmIcon"
                src="{{ asset('assets/icons/eyebrow.svg') }}"
                alt="Show password"
                class="w-4 h-4"
            >
        </button>
    </div>

    <!-- Password Requirements with circle.svg vs correct.svg -->
    <div id="password-requirements" class="text-sm mb-4">
        <p class="mb-1">Password must include:</p>
        <ul class="space-y-1 ml-5">
            <li class="flex items-center space-x-2" id="req-length">
                <span class="req-icon"></span>
                <span>At least 8 characters</span>
            </li>
            <li class="flex items-center space-x-2" id="req-number">
                <span class="req-icon"></span>
                <span>At least one number</span>
            </li>
            <li class="flex items-center space-x-2" id="req-uppercase">
                <span class="req-icon"></span>
                <span>At least one uppercase letter</span>
            </li>
            <li class="flex items-center space-x-2" id="req-special">
                <span class="req-icon"></span>
                <span>At least one special character (e.g., !@#$%^&*)</span>
            </li>
            <li class="flex items-center space-x-2" id="req-match">
                <span class="req-icon"></span>
                <span>Passwords must match</span>
            </li>
        </ul>
    </div>

    <!-- Submit Button -->
    <button
        id="submit-btn"
        disabled
        type="submit"
        class="w-full bg-indigo-600 text-white py-2 rounded-md opacity-50 transition"
    >
        Reset Password
    </button>
</form>
@endsection

@push('scripts')
<script>
function togglePassword(fieldId, iconId) {
    const passField = document.getElementById(fieldId);
    const iconField = document.getElementById(iconId);

    if (passField.type === 'password') {
        passField.type = 'text';
        iconField.src = '{{ asset('assets/icons/eye-close-up.svg') }}';
        iconField.alt = 'Hide password';
    } else {
        passField.type = 'password';
        iconField.src = '{{ asset('assets/icons/eyebrow.svg') }}';
        iconField.alt = 'Show password';
    }
}

const password        = document.getElementById('password');
const passwordConfirm = document.getElementById('password_confirmation');
const submitBtn       = document.getElementById('submit-btn');

// Requirement elements
const reqLength    = document.getElementById('req-length');
const reqNumber    = document.getElementById('req-number');
const reqUppercase = document.getElementById('req-uppercase');
const reqSpecial   = document.getElementById('req-special');
const reqMatch     = document.getElementById('req-match');

// Helper to set circle/correct icons
function setIconStatus(element, isGood) {
    const icon = element.querySelector('.req-icon');
    if (isGood) {
        // Show correct.svg
        icon.innerHTML = `
            <img src="{{ asset('assets/icons/correct.svg') }}"
                 alt="Correct" class="h-4 w-4"/>
        `;
    } else {
        // Show circle.svg
        icon.innerHTML = `
            <img src="{{ asset('assets/icons/circle.svg') }}"
                 alt="Not met" class="h-4 w-4"/>
        `;
    }
}

// Validate each requirement
function validatePassword() {
    const val        = password.value;
    const confirmVal = passwordConfirm.value;

    const isLength      = val.length >= 8;
    const hasNumber     = /[0-9]/.test(val);
    const hasUppercase  = /[A-Z]/.test(val);
    const hasSpecial    = /[!@#$%^&*(),.?":{}|<>]/.test(val);
    const passwordsMatch= (val === confirmVal && confirmVal !== '');

    setIconStatus(reqLength, isLength);
    setIconStatus(reqNumber, hasNumber);
    setIconStatus(reqUppercase, hasUppercase);
    setIconStatus(reqSpecial, hasSpecial);
    setIconStatus(reqMatch, passwordsMatch);

    const isValid = isLength && hasNumber && hasUppercase && hasSpecial && passwordsMatch;
    submitBtn.disabled = !isValid;
    submitBtn.classList.toggle('opacity-50', !isValid);
    submitBtn.classList.toggle('opacity-100', isValid);
}

// Attach event listeners
password.addEventListener('input', validatePassword);
passwordConfirm.addEventListener('input', validatePassword);

// Run once on load so icons appear immediately
validatePassword();
</script>
@endpush