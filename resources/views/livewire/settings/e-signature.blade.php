<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('E-Signature')" :subheading="__('Upload your electronic signature for reports')">
        <!-- Success Messages -->
        @if (session('status') === 'signature-updated')
            <div
                class="mb-6 rounded-lg bg-green-50 dark:bg-green-900/20 p-4 border border-green-200 dark:border-green-800">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800 dark:text-green-200">
                            {{ __('E-Signature uploaded successfully.') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if (session('status') === 'signature-deleted')
            <div
                class="mb-6 rounded-lg bg-green-50 dark:bg-green-900/20 p-4 border border-green-200 dark:border-green-800">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800 dark:text-green-200">
                            {{ __('E-Signature deleted successfully.') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Current Signature Display -->
        @if ($currentSignature)
            <div class="my-6">
                <flux:heading size="lg" class="mb-4">{{ __('Current Signature') }}</flux:heading>
                <div class="bg-white dark:bg-zinc-800 p-6 rounded-lg border border-zinc-200 dark:border-zinc-700">
                    <img src="{{ asset('storage/' . $currentSignature) }}" alt="Current E-Signature"
                        class="max-w-md h-auto border border-zinc-300 dark:border-zinc-600 rounded">

                    <flux:button wire:click="deleteSignature"
                        wire:confirm="Are you sure you want to delete your signature?" variant="danger" size="sm"
                        class="mt-4">
                        {{ __('Delete Signature') }}
                    </flux:button>
                </div>
            </div>
        @endif

        <!-- Upload Form -->
        <div class="my-6">
            <flux:heading size="lg" class="mb-4">
                {{ $currentSignature ? __('Replace Signature') : __('Upload Signature') }}
            </flux:heading>

            <form wire:submit="saveSignature" class="space-y-6">
                <div>
                    <flux:field>
                        <flux:label>{{ __('Select Signature Image') }}</flux:label>
                        <flux:description>{{ __('PNG, JPG, or JPEG. Maximum size: 2MB') }}</flux:description>
                        <input type="file" wire:model="signature" accept="image/png,image/jpeg,image/jpg"
                            class="mt-2 block w-full text-sm text-zinc-900 dark:text-zinc-100
                                   file:mr-4 file:py-2 file:px-4
                                   file:rounded-md file:border-0
                                   file:text-sm file:font-semibold
                                   file:bg-zinc-100 dark:file:bg-zinc-700
                                   file:text-zinc-700 dark:file:text-zinc-200
                                   hover:file:bg-zinc-200 dark:hover:file:bg-zinc-600
                                   border border-zinc-300 dark:border-zinc-600 rounded-md
                                   bg-white dark:bg-zinc-800">
                        <flux:error name="signature" />
                    </flux:field>

                    @if ($signature)
                        <div class="mt-4">
                            <flux:text class="mb-2">{{ __('Preview:') }}</flux:text>
                            <img src="{{ $signature->temporaryUrl() }}"
                                class="max-w-md h-auto border border-zinc-300 dark:border-zinc-600 rounded">
                        </div>
                    @endif
                </div>

                <div wire:loading wire:target="signature" class="text-sm text-zinc-600 dark:text-zinc-400">
                    {{ __('Uploading...') }}
                </div>

                <div class="flex items-center gap-4">
                    <flux:button variant="primary" type="submit" :disabled="!$signature">
                        {{ __('Save Signature') }}
                    </flux:button>

                    <x-action-message class="me-3" on="signature-updated">
                        {{ __('Saved.') }}
                    </x-action-message>
                </div>
            </form>
        </div>

        <!-- Information Section -->
        <div class="my-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
            <flux:heading size="sm" class="mb-2 text-blue-900 dark:text-blue-100">
                {{ __('Tips for a good signature image:') }}
            </flux:heading>
            <ul class="list-disc list-inside text-sm text-blue-800 dark:text-blue-200 space-y-1">
                <li>{{ __('Use a clear image with a transparent or white background') }}</li>
                <li>{{ __('Ensure your signature is clearly visible and legible') }}</li>
                <li>{{ __('Recommended dimensions: 400x150 pixels or similar aspect ratio') }}</li>
                <li>{{ __('The signature will be used in generated reports') }}</li>
            </ul>
        </div>
    </x-settings.layout>
</section>
