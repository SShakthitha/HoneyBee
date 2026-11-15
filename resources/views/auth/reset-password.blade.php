<x-guest-layout>
    <div class="auth-card-intro">
        <span class="auth-eyebrow">STEP 1 OF 2</span>
        <h1>Verify your email</h1>
        <p>Enter the 6-digit code we sent to {{ $email }}.</p>
    </div>

    @if (session('status'))
        <div class="mb-5 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800" role="status">
            <i class="fa-solid fa-envelope-open-text mr-2 text-green-700" aria-hidden="true"></i>
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.verify') }}" id="verify-otp-form">
        @csrf
        <input type="hidden" name="email" value="{{ old('email', $email ?? '') }}">

        <div class="auth-field">
            <x-input-label for="otp" :value="__('6-digit verification code')" />
            <x-text-input
                id="otp"
                class="auth-input mt-2 block w-full text-center font-semibold tracking-[0.45em] text-xl"
                type="text"
                name="otp"
                inputmode="numeric"
                pattern="[0-9]{6}"
                minlength="6"
                maxlength="6"
                required
                autofocus
                autocomplete="one-time-code"
                placeholder="••••••"
                aria-describedby="otp-hint"
            />
            <p id="otp-hint" class="mt-2 text-sm text-stone-500">The code expires in {{ config('password_otp.expiry', 10) }} minutes.</p>
            <x-input-error :messages="$errors->get('otp')" class="mt-2" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-6">
            <x-primary-button id="verify-submit" class="auth-submit w-full justify-center">
                <span id="verify-label">Verify code</span>
                <span id="verify-loading" class="hidden" role="status" aria-live="polite">Checking code...</span>
            </x-primary-button>
        </div>

        <div class="auth-register-prompt">
            <p>Didn't receive the email? <a class="auth-link" href="{{ route('password.request') }}">Request another code</a></p>
        </div>
        <div class="mt-4 text-center">
            <a class="auth-link text-sm" href="{{ route('login') }}">&larr; Back to login</a>
        </div>
    </form>

    <script>
        const otpField = document.getElementById('otp');
        otpField.addEventListener('input', () => {
            otpField.value = otpField.value.replace(/\D/g, '').slice(0, 6);
        });

        document.getElementById('verify-otp-form').addEventListener('submit', () => {
            const button = document.getElementById('verify-submit');
            button.disabled = true;
            document.getElementById('verify-label').classList.add('hidden');
            document.getElementById('verify-loading').classList.remove('hidden');
        });
    </script>
</x-guest-layout>
