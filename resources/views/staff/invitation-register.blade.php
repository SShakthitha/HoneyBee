<x-guest-layout>
    @if ($problem)
        <div class="rounded-md border border-amber-500/40 bg-amber-50 p-4 text-sm text-amber-900" role="alert">
            {{ $problem }}
        </div>

        <div style="text-align: center; margin-top: 20px;">
            <p style="color: white;">
                Already have an account?
                <a href="{{ route('login') }}" style="color: #f5a623; font-weight: bold;">
                    Login here
                </a>
            </p>
        </div>
    @else
        <form method="POST" action="{{ route('staff.invitation.store', ['token' => $token]) }}">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="name" :value="__('Name')" />
                    <x-text-input id="name" class="block mt-1 w-full" type="text" :value="$invitation->staff->full_name" readonly aria-readonly="true" />
                </div>

                <div>
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" class="block mt-1 w-full" type="email" :value="$invitation->staff->email" readonly aria-readonly="true" />
                </div>

                <div class="relative">
                    <x-input-label for="password" :value="__('Password')" />
                    <x-text-input id="password" class="block mt-1 w-full pr-10" type="password" name="password" required autocomplete="new-password" aria-describedby="password-guidance" />
                    <button type="button" onclick="toggleInvitationPassword('password', this)" class="absolute right-3 top-9 text-gray-500 hover:text-amber-500" aria-label="Show password">
                        <i class="fa-solid fa-eye" aria-hidden="true"></i>
                    </button>
                    <p id="password-guidance" class="mt-1 text-sm text-white">Use at least 12 characters with upper and lower case letters, a number, and a symbol.</p>
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div class="relative">
                    <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                    <x-text-input id="password_confirmation" class="block mt-1 w-full pr-10" type="password" name="password_confirmation" required autocomplete="new-password" />
                    <button type="button" onclick="toggleInvitationPassword('password_confirmation', this)" class="absolute right-3 top-9 text-gray-500 hover:text-amber-500" aria-label="Show confirmation password">
                        <i class="fa-solid fa-eye" aria-hidden="true"></i>
                    </button>
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>
            </div>

            <div class="mt-6">
                <x-primary-button class="w-full justify-center">
                    {{ __('Register') }}
                </x-primary-button>
            </div>

            <div style="text-align: center; margin-top: 20px;">
                <p style="color: white;">
                    Already have an account?
                    <a href="{{ route('login') }}" style="color: #f5a623; font-weight: bold;">
                        Login here
                    </a>
                </p>
            </div>
        </form>
    @endif

    <script>
        function toggleInvitationPassword(fieldId, button) {
            const field = document.getElementById(fieldId);
            const icon = button.querySelector('i');
            const showing = field.type === 'password';

            field.type = showing ? 'text' : 'password';
            icon.classList.toggle('fa-eye', !showing);
            icon.classList.toggle('fa-eye-slash', showing);
            button.setAttribute('aria-label', showing ? 'Hide password' : 'Show password');
        }
    </script>
</x-guest-layout>
