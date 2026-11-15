<x-guest-layout>

    {{-- Page heading --}}
    <h2 class="text-2xl font-bold text-center mb-1" style="color: #f5a623;">
        Forgot Password?
    </h2>
    <p class="text-sm text-center text-gray-400 mb-6">
        Enter your email address and we'll send you a 6-digit reset code.
    </p>

    {{-- Success status (shown after submitting) --}}
    @if (session('status'))
        <div class="mb-5 flex items-start gap-3 rounded-lg border border-green-500/40 bg-green-900/30 px-4 py-3 text-sm text-green-300">
            <i class="fa-solid fa-circle-check mt-0.5 shrink-0 text-green-400"></i>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" id="forgot-form">
        @csrf

        {{-- Email --}}
        <div>
            <x-input-label for="email" :value="__('Email Address')" class="text-gray-300" />
            <x-text-input
                id="email"
                class="block mt-1 w-full"
                type="email"
                name="email"
                :value="old('email')"
                required
                autofocus
                autocomplete="email"
                placeholder="you@example.com"
            />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-6">
            <x-primary-button id="forgot-submit" class="w-full justify-center">
                <span id="forgot-label">{{ __('Send Reset Code') }}</span>
                <span id="forgot-loading" class="hidden" role="status" aria-live="polite">
                    <i class="fa-solid fa-spinner fa-spin mr-1"></i> Sending…
                </span>
            </x-primary-button>
        </div>

        <div class="mt-5 text-center">
            <a href="{{ route('login') }}"
               class="text-sm text-amber-400 hover:text-amber-300 underline">
                &larr; Back to Login
            </a>
        </div>
    </form>

    <script>
        document.getElementById('forgot-form').addEventListener('submit', function () {
            const btn = document.getElementById('forgot-submit');
            btn.disabled = true;
            document.getElementById('forgot-label').classList.add('hidden');
            document.getElementById('forgot-loading').classList.remove('hidden');
        });
    </script>

</x-guest-layout>
