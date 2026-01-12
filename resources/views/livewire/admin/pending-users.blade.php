<div>
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Pending Users</h1>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Manage users waiting for unit component assignment</p>
    </div>

    @if (session()->has('success'))
        <div class="mb-4 bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500 p-4 rounded-r-lg">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-green-600 dark:text-green-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-sm text-green-700 dark:text-green-400">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    <div class="bg-white dark:bg-neutral-900 rounded-xl border border-neutral-200 dark:border-neutral-700 overflow-hidden">
        <livewire:admin.pending-users-table />
    </div>

    {{-- Assign Unit Component Modal --}}
    <flux:modal name="assign-unit-modal" wire:model="showAssignModal" class="max-w-md">
        <form wire:submit.prevent="assignUnitComponent">
            <div class="p-6">
                <flux:heading size="lg" class="mb-2">Assign Unit Component</flux:heading>
                <flux:subheading class="mb-6">
                    @if ($selectedUser)
                        Assign a unit component to <strong>{{ $selectedUser->name }}</strong>
                    @endif
                </flux:subheading>

                @if ($selectedUser)
                    <div class="space-y-4 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">
                                Email
                            </label>
                            <p class="text-base text-zinc-900 dark:text-white">{{ $selectedUser->email }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">
                                Designation
                            </label>
                            <p class="text-base text-zinc-900 dark:text-white">{{ $selectedUser->designation ?? 'N/A' }}</p>
                        </div>
                    </div>
                @endif

                <div class="mb-6">
                    <flux:field>
                        <flux:label>Unit Component *</flux:label>
                        <flux:select wire:model="selectedUnitComponent" placeholder="Select unit component...">
                            <option value="">Select unit component...</option>
                            <option value="IBUILD">IBUILD</option>
                            <option value="IREAP">IREAP</option>
                            <option value="IPLAN">IPLAN</option>
                            <option value="GGU">GGU</option>
                            <option value="SES">SES</option>
                            <option value="MEL">MEL</option>
                            <option value="INFOACE">INFOACE</option>
                            <option value="PROCUREMENT">PROCUREMENT</option>
                            <option value="FINANCE">FINANCE</option>
                            <option value="IDU">IDU</option>
                            <option value="ADMIN">ADMIN</option>
                        </flux:select>
                        @error('selectedUnitComponent')
                            <flux:error>{{ $message }}</flux:error>
                        @enderror
                    </flux:field>
                </div>
            </div>

            <div class="flex gap-2 justify-end p-6 border-t border-zinc-200 dark:border-zinc-700">
                <flux:button type="button" wire:click="closeModal" variant="ghost">
                    Cancel
                </flux:button>
                <flux:button type="submit" variant="primary">
                    Assign Unit Component
                </flux:button>
            </div>
        </form>
    </flux:modal>
</div>
