<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />

            <x-text-input id="email"
                          class="block mt-1 w-full"
                          type="email"
                          name="email"
                          :value="old('email')"
                          required
                          autofocus
                          autocomplete="username" />

            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <!-- Password -->
<div class="mt-4">
    <x-input-label for="password" :value="__('Password')" />

    <div class="relative">
        <x-text-input id="password"
                      class="block mt-1 w-full pr-10"
                      type="password"
                      name="password"
                      required
                      autocomplete="current-password" />

        <button type="button"
                onclick="togglePassword('password', this)"
                class="absolute right-3 top-3 text-gray-500 hover:text-amber-500">
            <i class="fa-solid fa-eye"></i>
        </button>
    </div>

    <x-input-error :messages="$errors->get('password')" class="mt-2" />
</div>


        <!-- Login Button (Full Width) -->
        <div class="mt-6">
            <x-primary-button class="w-full justify-center">
                {{ __('Log in') }}
            </x-primary-button>
        </div>


        <!-- Forgot Password + Remember Me -->
        <div class="flex items-center justify-between mt-4">

            @if (Route::has('password.request'))
                <a class="underline text-sm rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                   style="color: #f5a623; font-weight: bold;"
                   href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif


            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me"
                       type="checkbox"
                       class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                       name="remember">

                <span class="ms-2 text-sm" style="color: #f5a623; font-weight: bold;">
                    {{ __('Remember me') }}
                </span>
            </label>

        </div>


        <!-- Register Link -->
        <div style="text-align: center; margin-top: 20px;">
            <p style="color: white;">
                Don't have an account?
                <a href="{{ route('register') }}" 
                   style="color: #f5a623; font-weight: bold;">
                    Register here
                </a>
            </p>
        </div>
<script>
function togglePassword(fieldId, button) {
    const field = document.getElementById(fieldId);
    const icon = button.querySelector('i');

    if (field.type === "password") {
        field.type = "text";
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");
    } else {
        field.type = "password";
        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");
    }
}
</script>
    </form>
</x-guest-layout>