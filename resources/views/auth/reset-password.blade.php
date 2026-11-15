<x-guest-layout>

    {{-- Page heading --}}
    <h2 class="text-2xl font-bold text-center mb-1" style="color: #f5a623;">
        Reset Your Password
    </h2>
    <p class="text-sm text-center text-gray-400 mb-6">
        Enter the 6-digit code from your email and choose a new password.
    </p>

    {{-- Status / info message passed from ForgotPasswordController --}}
    @if (session('status'))
        <div class="mb-5 flex items-start gap-3 rounded-lg border border-green-500/40 bg-green-900/30 px-4 py-3 text-sm text-green-300">
            <i class="fa-solid fa-envelope-open-text mt-0.5 shrink-0 text-green-400"></i>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    <form method="POST" action="{{ route('password.store') }}" id="reset-form">
        @csrf

        {{-- Email --}}
        <div>
            <x-input-label for="email" :value="__('Email Address')" class="text-gray-300" />
            <x-text-input
                id="email"
                class="block mt-1 w-full"
                type="email"
                name="email"
                :value="old('email', $email ?? '')"
                required
                autofocus
                autocomplete="email"
                placeholder="you@example.com"
            />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        {{-- OTP Code --}}
        <div class="mt-5">
            <x-input-label for="otp" :value="__('6-Digit Reset Code')" class="text-gray-300" />
            <x-text-input
                id="otp"
                class="block mt-1 w-full tracking-[0.4em] text-center text-lg font-semibold"
                type="text"
                name="otp"
                inputmode="numeric"
                pattern="\d{6}"
                maxlength="6"
                required
                autocomplete="one-time-code"
                placeholder="• • • • • •"
            />
            <p class="mt-1 text-xs text-gray-500">Check your inbox for the code. It expires in 10 minutes.</p>
            <x-input-error :messages="$errors->get('otp')" class="mt-2" />
        </div>

        {{-- New Password --}}
        <div class="mt-5">
            <x-input-label for="password" :value="__('New Password')" class="text-gray-300" />
            <div class="relative">
                <x-text-input
                    id="password"
                    class="block mt-1 w-full pr-10"
                    type="password"
                    name="password"
                    required
                    autocomplete="new-password"
                    placeholder="Min. 8 characters"
                />
                <button type="button"
                        onclick="togglePassword('password', this)"
                        aria-label="Show password"
                        aria-controls="password"
                        aria-pressed="false"
                        class="absolute right-3 top-3 text-gray-500 hover:text-amber-500">
                    <i class="fa-solid fa-eye" aria-hidden="true"></i>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        {{-- Confirm Password --}}
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm New Password')" class="text-gray-300" />
            <div class="relative">
                <x-text-input
                    id="password_confirmation"
                    class="block mt-1 w-full pr-10"
                    type="password"
                    name="password_confirmation"
                    required
                    autocomplete="new-password"
                    placeholder="Repeat new password"
                />
                <button type="button"
                        onclick="togglePassword('password_confirmation', this)"
                        aria-label="Show confirm password"
                        aria-controls="password_confirmation"
                        aria-pressed="false"
                        class="absolute right-3 top-3 text-gray-500 hover:text-amber-500">
                    <i class="fa-solid fa-eye" aria-hidden="true"></i>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="mt-6">
            <x-primary-button id="reset-submit" class="w-full justify-center">
                <span id="reset-label">{{ __('Reset Password') }}</span>
                <span id="reset-loading" class="hidden" role="status" aria-live="polite">
                    <i class="fa-solid fa-spinner fa-spin mr-1"></i> Resetting…
                </span>
            </x-primary-button>
        </div>

        <div class="mt-5 text-center">
            <a href="{{ route('password.request') }}"
               class="text-sm text-amber-400 hover:text-amber-300 underline">
                Didn't receive the code? Request a new one
            </a>
        </div>

        <div class="mt-3 text-center">
            <a href="{{ route('login') }}"
               class="text-sm text-gray-500 hover:text-gray-300 underline">
                &larr; Back to Login
            </a>
        </div>
    </form>

    <script>
        function togglePassword(fieldId, button) {
            const field = document.getElementById(fieldId);
            const icon = button.querySelector('i');
            const isHidden = field.type === 'password';

            field.type = isHidden ? 'text' : 'password';
            icon.classList.toggle('fa-eye', !isHidden);
            icon.classList.toggle('fa-eye-slash', isHidden);
            button.setAttribute('aria-label', isHidden ? 'Hide password' : 'Show password');
            button.setAttribute('aria-pressed', isHidden ? 'true' : 'false');
        }

        document.getElementById('reset-form').addEventListener('submit', function () {
            const btn = document.getElementById('reset-submit');
            btn.disabled = true;
            document.getElementById('reset-label').classList.add('hidden');
            document.getElementById('reset-loading').classList.remove('hidden');
        });

        // Auto-advance: move focus to password field once 6 digits are typed
        const otpInput = document.getElementById('otp');
        otpInput.addEventListener('input', function () {
            this.value = this.value.replace(/\D/g, '').slice(0, 6);
            if (this.value.length === 6) {
                document.getElementById('password').focus();
            }
        });
    </script>

</x-guest-layout>
