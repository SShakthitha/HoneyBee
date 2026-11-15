<x-guest-layout>
    <div class="auth-card-intro">
        <span class="auth-eyebrow">HONEYBEE GIFTS &amp; DESIGNS</span>
        <h1>Welcome back</h1>
        <p>Sign in to continue to your account.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="auth-status mb-4" :status="session('status')" />

    <form id="login-form" class="auth-form" method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div class="auth-field">
            <x-input-label for="email" :value="__('Email')" />

            <x-text-input id="email"
                          class="auth-input block mt-1 w-full"
                          type="email"
                          name="email"
                          :value="old('email')"
                          required
                          autofocus
                          autocomplete="username" />

            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="auth-field mt-5">
            <x-input-label for="password" :value="__('Password')" />

            <div class="relative">
                <x-text-input id="password"
                              class="auth-input block mt-1 w-full pr-10"
                              type="password"
                              name="password"
                              required
                              autocomplete="current-password" />

                <button type="button"
                        onclick="togglePassword('password', this)"
                        aria-label="Show password"
                        aria-controls="password"
                        aria-pressed="false"
                        class="auth-password-toggle absolute right-3 top-3 text-gray-500 hover:text-amber-500">
                    <i class="fa-solid fa-eye" aria-hidden="true"></i>
                </button>
            </div>

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>


        <!-- Login Button (Full Width) -->
        <div class="mt-6">
            <x-primary-button id="login-submit" class="auth-submit w-full justify-center">
                <span id="login-submit-label">{{ __('Log in') }}</span>
                <span id="login-loading" class="hidden" role="status" aria-live="polite">
                    {{ __('Signing in…') }}
                </span>
            </x-primary-button>
        </div>


        <!-- Forgot Password + Remember Me -->
        <div class="auth-options flex items-center justify-between mt-5">

            @if (Route::has('password.request'))
                <a class="auth-link underline text-sm rounded-md focus:outline-none focus:ring-2 focus:ring-amber-500"
                   href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif


            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me"
                       type="checkbox"
                       class="auth-checkbox rounded border-gray-300 text-amber-600 shadow-sm focus:ring-amber-500"
                       name="remember">

                <span class="ms-2 text-sm auth-remember-label">
                    {{ __('Remember me') }}
                </span>
            </label>

        </div>


        <!-- Register Link -->
        <div class="auth-register-prompt">
            <p>
                Don't have an account?
                <a class="auth-link" href="{{ route('register') }}">
                    Register here
                </a>
            </p>
        </div>
<script>
function togglePassword(fieldId, button) {
    const field = document.getElementById(fieldId);
    const icon = button.querySelector('i');
    const isVisible = field.type === "password";

    if (isVisible) {
        field.type = "text";
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");
        button.setAttribute("aria-label", "Hide password");
        button.setAttribute("aria-pressed", "true");
    } else {
        field.type = "password";
        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");
        button.setAttribute("aria-label", "Show password");
        button.setAttribute("aria-pressed", "false");
    }
}

document.getElementById('login-form').addEventListener('submit', function () {
    const submitButton = document.getElementById('login-submit');
    submitButton.disabled = true;
    document.getElementById('login-submit-label').classList.add('hidden');
    document.getElementById('login-loading').classList.remove('hidden');
});
</script>
    </form>
</x-guest-layout>
