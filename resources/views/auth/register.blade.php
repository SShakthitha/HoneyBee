<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
<div class="mt-4">
    <x-input-label for="password" :value="__('Password')" />
    <div style="position: relative;">
        <x-text-input id="password" class="block mt-1 w-full"
                        type="password"
                        name="password"
                        required autocomplete="new-password" />
        <span onclick="togglePassword('password', this)"
            style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer; font-size: 18px;">
            👁️
        </span>
    </div>
    <x-input-error :messages="$errors->get('password')" class="mt-2" />
</div>

<!-- Confirm Password -->
<div class="mt-4">
    <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
    <div style="position: relative;">
        <x-text-input id="password_confirmation" class="block mt-1 w-full"
                        type="password"
                        name="password_confirmation"
                        required autocomplete="new-password" />
        <span onclick="togglePassword('password_confirmation', this)"
            style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer; font-size: 18px;">
            👁️
        </span>
    </div>
    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
</div>
        <div style="text-align: center; margin-top: 20px;">
          <x-primary-button style="width: 100%; justify-content: center;">
          {{ __('Register') }}
          </x-primary-button>

          <div style="margin-top: 15px;">
            <a href="{{ route('login') }}" style="color: #f5a623; font-weight: bold;">
              Already registered? Login here
            </a>
          </div>
        </div>
    </form>
    <script>
      function togglePassword(fieldId, icon) {
        const field = document.getElementById(fieldId);
        if (field.type === 'password') {
            field.type = 'text';
            icon.textContent = '🙈';
        } else {
            field.type = 'password';
            icon.textContent = '👁️';
        }
      }
    </script>
</x-guest-layout>
