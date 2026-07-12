<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf
        <!-- Name + Email -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">

    <!-- Name -->
    <div>
        <x-input-label for="name" :value="__('Name')" />

        <x-text-input id="name"
                      class="block mt-1 w-full"
                      type="text"
                      name="name"
                      :value="old('name')"
                      required
                      autofocus
                      autocomplete="name" />

        <x-input-error :messages="$errors->get('name')" class="mt-2" />
    </div>


    <!-- Email -->
    <div>
        <x-input-label for="email" :value="__('Email')" />

        <x-text-input id="email"
                      class="block mt-1 w-full"
                      type="email"
                      name="email"
                      :value="old('email')"
                      required
                      autocomplete="username" />

        <x-input-error :messages="$errors->get('email')" class="mt-2" />
    </div>


    <!-- Phone -->
    <div>
        <x-input-label for="phone" :value="__('Phone')" />

        <x-text-input id="phone"
                      class="block mt-1 w-full"
                      type="text"
                      name="phone"
                      :value="old('phone')"
                      required />

        <x-input-error :messages="$errors->get('phone')" class="mt-2" />
    </div>


    <!-- Password -->
<div class="relative">

    <x-input-label for="password" :value="__('Password')" />

    <x-text-input id="password"
                  class="block mt-1 w-full pr-10"
                  type="password"
                  name="password"
                  required />

    <button type="button"
            onclick="togglePassword('password', this)"
            class="absolute right-3 top-9 text-gray-500 hover:text-amber-500">
        <i class="fa-solid fa-eye"></i>
    </button>

    <x-input-error :messages="$errors->get('password')" class="mt-2" />

</div>


    <!-- Address -->
    <div>
        <x-input-label for="address" :value="__('Address')" />

        <textarea id="address"
                  name="address"
                  class="block mt-1 w-full border-gray-300 rounded-md shadow-sm"
                  required>{{ old('address') }}</textarea>

        <x-input-error :messages="$errors->get('address')" class="mt-2" />
    </div>


    <!-- Confirm Password -->
<div class="relative">

    <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

    <x-text-input id="password_confirmation"
                  class="block mt-1 w-full pr-10"
                  type="password"
                  name="password_confirmation"
                  required />

    <button type="button"
            onclick="togglePassword('password_confirmation', this)"
            class="absolute right-3 top-9 text-gray-500 hover:text-amber-500">
        <i class="fa-solid fa-eye"></i>
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
</x-guest-layout>
