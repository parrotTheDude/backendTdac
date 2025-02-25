<!-- resources/views/auth/set-password.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify & Set Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen px-4">
    <div class="bg-white p-6 sm:p-8 rounded-lg shadow-md w-full max-w-md mx-auto">

        <!-- TDAC Logo (or whichever you prefer) -->
        <div class="mb-4 flex justify-center">
            <img src="https://thatdisabilityadventurecompany.com.au/icons/logo.webp"
                 alt="TDAC Logo"
                 class="w-48 h-auto">
        </div>

        <h2 class="text-xl sm:text-2xl font-semibold text-center mb-6">Set Your Password</h2>

        @if($errors->any())
            <div class="text-red-500 mb-4">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('verify.savePassword') }}">
            @csrf
            <!-- Include the token from the URL -->
            <input type="hidden" name="token" value="{{ $token }}">

            <!-- The new password fields -->
            <div class="mb-4">
                <input name="password" id="password" type="password" required placeholder="New Password"
                    class="w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-indigo-500">
            </div>

            <div class="mb-4">
                <input name="password_confirmation" id="password_confirmation" type="password" required placeholder="Confirm Password"
                    class="w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-indigo-500">
            </div>

            <!-- Password Requirements UI -->
            <div id="password-requirements" class="text-sm mb-4">
                <p class="mb-1">Password must include:</p>
                <ul class="list-disc ml-5">
                    <li id="req-length" class="text-gray-500">At least 8 characters</li>
                    <li id="req-number" class="text-gray-500">At least one number</li>
                    <li id="req-uppercase" class="text-gray-500">At least one uppercase letter</li>
                    <li id="req-special" class="text-gray-500">At least one special character (e.g., !@#$%^&*)</li>
                    <li id="req-match" class="text-gray-500">Passwords must match</li>
                </ul>
            </div>

            <!-- Subscribe Checkbox -->
            <div class="mb-4 flex items-center space-x-2">
                <input type="checkbox" name="subscribe" id="subscribe" value="1"
                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                <label for="subscribe" class="text-sm text-gray-700">
                    Subscribe to our newsletter
                </label>
            </div>

            <button id="submit-btn" disabled type="submit"
                class="w-full bg-indigo-600 text-white py-2 rounded-md opacity-50 transition">
                Save Password
            </button>
        </form>

    </div>

    <!-- The same password validation JS from your reset form -->
    <script>
        const password = document.getElementById('password');
        const passwordConfirmation = document.getElementById('password_confirmation');
        const submitBtn = document.getElementById('submit-btn');

        const reqLength = document.getElementById('req-length');
        const reqNumber = document.getElementById('req-number');
        const reqUppercase = document.getElementById('req-uppercase');
        const reqSpecial = document.getElementById('req-special');
        const reqMatch = document.getElementById('req-match');

        function validatePassword() {
            const val = password.value;
            const confirmVal = passwordConfirmation.value;

            const isLength = val.length >= 8;
            const hasNumber = /[0-9]/.test(val);
            const hasUppercase = /[A-Z]/.test(val);
            const hasSpecial = /[!@#$%^&*(),.?\":{}|<>]/.test(val);
            const passwordsMatch = val === confirmVal && confirmVal !== '';

            reqLength.classList.toggle('text-green-500', isLength);
            reqLength.classList.toggle('text-gray-500', !isLength);

            reqNumber.classList.toggle('text-green-500', hasNumber);
            reqNumber.classList.toggle('text-gray-500', !hasNumber);

            reqUppercase.classList.toggle('text-green-500', hasUppercase);
            reqUppercase.classList.toggle('text-gray-500', !hasUppercase);

            reqSpecial.classList.toggle('text-green-500', hasSpecial);
            reqSpecial.classList.toggle('text-gray-500', !hasSpecial);

            reqMatch.classList.toggle('text-green-500', passwordsMatch);
            reqMatch.classList.toggle('text-gray-500', !passwordsMatch);

            const isValid = isLength && hasNumber && hasUppercase && hasSpecial && passwordsMatch;

            submitBtn.disabled = !isValid;
            submitBtn.classList.toggle('opacity-50', !isValid);
            submitBtn.classList.toggle('opacity-100', isValid);
        }

        password.addEventListener('input', validatePassword);
        passwordConfirmation.addEventListener('input', validatePassword);
    </script>
</body>
</html>