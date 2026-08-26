<div class="flex flex-col gap-6">
     <x-auth-header :title="__('Ingresar')" :description="__('Ingrese su email y contraseña luego ingresar')" />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

<form wire:submit="login" class="flex flex-col gap-6">
        <!-- Email Address -->
        <flux:input
            wire:model="email"
            :label="__('Email address')"
            type="email"
            required
            autofocus
            autocomplete="email"
            placeholder="email@example.com"
        /> 

        <!-- Password -->
         <div class="relative">
            <flux:input
                wire:model="password"
                :label="__('Password')"
                type="password"
                required
                autocomplete="current-password"
                :placeholder="__('Password')"
                viewable
            />

        </div> 

        <!-- Remember Me -->
        <flux:checkbox wire:model="remember" :label="__('Remember me')" />

        <div class="flex items-center justify-end">
            <flux:button variant="primary" type="submit" class="w-full">{{ __('Ingresar') }}</flux:button>
        </div>
    </form> 

    <div class="flex justify-center mt-6">
        <a href="{{ url('/auth/google') }}"
        class="flex items-center gap-3 bg-white border border-gray-300 rounded-lg px-4 py-2 shadow hover:shadow-md transition text-sm text-gray-700">
            <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg"
                alt="Google Logo"
                class="w-5 h-5" />
            {{ __('Ingresar por Google') }}
        </a>
    </div>

</div>
