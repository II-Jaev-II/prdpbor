<x-layouts.auth title="Register">
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Create an account')" :description="__('Enter your details below to create your account')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('register.store') }}" class="flex flex-col gap-6"
            enctype="multipart/form-data">
            @csrf

            <!-- Name -->
            <flux:input name="name" :label="__('Name')" :value="old('name')" type="text" required autofocus
                autocomplete="name" :placeholder="__('Full name')" />

            <!-- Designation -->
            <flux:input name="designation" :label="__('Designation')" :value="old('designation')" type="text" required
                autocomplete="organization-title" :placeholder="__('Enter your designation')" />

            <!-- Email Address -->
            <flux:input name="email" :label="__('Email address')" :value="old('email')" type="email" required
                autocomplete="email" placeholder="email@example.com" />

            <!-- Password -->
            <flux:input name="password" :label="__('Password')" type="password" required autocomplete="new-password"
                :placeholder="__('Password')" viewable />

            <!-- Confirm Password -->
            <flux:input name="password_confirmation" :label="__('Confirm password')" type="password" required
                autocomplete="new-password" :placeholder="__('Confirm password')" viewable />

            <!-- E-Signature -->
            <flux:field>
                <flux:label>{{ __('E-Signature') }}</flux:label>
                <flux:description>{{ __('Upload your e-signature image (max 2MB)') }}</flux:description>
                <flux:input name="e_signature" type="file" accept="image/*" required />
            </flux:field>

            <div class="flex items-center justify-end">
                <flux:button type="submit" variant="primary" class="w-full">
                    {{ __('Create account') }}
                </flux:button>
            </div>
        </form>

        <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
            <span>{{ __('Already have an account?') }}</span>
            <flux:link :href="route('login')" wire:navigate>{{ __('Log in') }}</flux:link>
        </div>
    </div>
</x-layouts.auth>
