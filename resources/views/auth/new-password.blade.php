<x-guest-layout>
    <div class="auth-card-intro">
        <span class="auth-eyebrow">STEP 2 OF 2</span>
        <h1>Create a new password</h1>
        <p>Your email is verified. Choose a new password for your account.</p>
    </div>

    <form method="POST" action="{{ route('password.store') }}" id="new-password-form">
        @csrf

        <div class="auth-field">
            <x-input-label for="password" :value="__('New password')" />
            <div class="relative mt-2">
                <x-text-input
                    id="password"
                    class="auth-input block w-full pr-12"
                    type="password"
                    name="password"
                    required
                    autofocus
                    autocomplete="new-password"
                    placeholder="Enter your new password"
                />
                <button type="button" onclick="togglePassword('password', this)" aria-label="Show password" aria-controls="password" aria-pressed="false" class="auth-password-toggle absolute right-3 top-3 text-gray-500 hover:text-amber-500">
                    <i class="fa-solid fa-eye" aria-hidden="true"></i>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="auth-field mt-5">
            <x-input-label for="password_confirmation" :value="__('Confirm new password')" />
            <div class="relative mt-2">
                <x-text-input
                    id="password_confirmation"
                    class="auth-input block w-full pr-12"
                    type="password"
                    name="password_confirmation"
                    required
                    autocomplete="new-password"
                    placeholder="Enter the same password again"
                />
                <button type="button" onclick="togglePassword('password_confirmation', this)" aria-label="Show confirmation password" aria-controls="password_confirmation" aria-pressed="false" class="auth-password-toggle absolute right-3 top-3 text-gray-500 hover:text-amber-500">
                    <i class="fa-solid fa-eye" aria-hidden="true"></i>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="mt-6">
            <x-primary-button id="new-password-submit" class="auth-submit w-full justify-center">
                <span id="new-password-label">Save new password</span>
                <span id="new-password-loading" class="hidden" role="status" aria-live="polite">Updating password...</span>
            </x-primary-button>
        </div>
    </form>

    <script>
        function togglePassword(fieldId, button) {
            const field = document.getElementById(fieldId);
            const icon = button.querySelector('i');
            const show = field.type === 'password';
            field.type = show ? 'text' : 'password';
            icon.classList.toggle('fa-eye', !show);
            icon.classList.toggle('fa-eye-slash', show);
            button.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
            button.setAttribute('aria-pressed', show ? 'true' : 'false');
        }

        document.getElementById('new-password-form').addEventListener('submit', () => {
            const button = document.getElementById('new-password-submit');
            button.disabled = true;
            document.getElementById('new-password-label').classList.add('hidden');
            document.getElementById('new-password-loading').classList.remove('hidden');
        });
    </script>
</x-guest-layout>
