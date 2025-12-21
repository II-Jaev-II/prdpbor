<div>
    <livewire:pending-table />

    {{-- Edit Modal --}}
    <flux:modal name="edit-report-modal" wire:model="showEditModal" class="min-w-full min-h-screen m-0 rounded-none">
        <form wire:submit="updateReport" class="h-full flex flex-col">
            <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                <flux:heading size="lg">Edit Report</flux:heading>
                <flux:subheading>Update the report details below</flux:subheading>
            </div>

            <div class="flex-1 overflow-y-auto p-6">
                <div class="max-w-4xl mx-auto space-y-6">
                    <div>
                        <label for="editForm.date_of_travel"
                            class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                            Date of Travel
                        </label>
                        <div wire:ignore x-data="{
                            picker: null,
                            dateValue: @entangle('editForm.date_of_travel').live,
                            init() {
                                this.$nextTick(() => {
                                    this.picker = flatpickr(this.$refs.datePicker, {
                                        mode: 'range',
                                        dateFormat: 'Y-m-d',
                                        enableTime: false,
                                        altInput: true,
                                        altFormat: 'F j, Y',
                                        appendTo: this.$root,
                                        static: false,
                                        onChange: (selectedDates, dateStr) => {
                                            this.dateValue = dateStr;
                                        }
                                    });
                                });
                            },
                            destroy() {
                                if (this.picker) {
                                    this.picker.destroy();
                                    this.picker = null;
                                }
                            }
                        }" x-on:modal-close.window="destroy()" x-watch="dateValue"
                            x-effect="if (picker && dateValue) { picker.setDate(dateValue.split(' to '), false); }">
                            <input type="text" x-ref="datePicker" id="editForm.date_of_travel"
                                wire:model="editForm.date_of_travel"
                                class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white dark:focus:border-blue-500">
                        </div>
                        @error('editForm.date_of_travel')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Purpose --}}
                    <flux:select wire:model="editForm.purpose" label="Purpose">
                        <option value="">Select Purpose</option>
                        <option value="Meeting">Meeting</option>
                        <option value="Field Visit">Field Visit</option>
                        <option value="Training">Training</option>
                        <option value="Inspection">Inspection</option>
                        <option value="Conference">Conference</option>
                        <option value="Other">Other</option>
                    </flux:select>

                    {{-- Place --}}
                    <flux:input wire:model="editForm.place" label="Place" type="text"
                        placeholder="Enter location/place" />

                    {{-- Accomplishment --}}
                    <flux:textarea wire:model="editForm.accomplishment" label="Accomplishment" rows="4"
                        placeholder="Describe what was accomplished..." />

                    {{-- Existing Photos --}}
                    @if (!empty($existingPhotos))
                        <div>
                            <label class="block text-sm font-medium text-gray-900 dark:text-white mb-3">
                                Existing Photos
                            </label>
                            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                                @foreach ($existingPhotos as $index => $photo)
                                    <div class="relative group">
                                        <a href="{{ Storage::url($photo) }}" target="_blank" rel="noopener noreferrer"
                                            class="block">
                                            <img src="{{ Storage::url($photo) }}"
                                                class="h-40 w-full rounded-lg object-cover border border-gray-200 dark:border-gray-700"
                                                alt="Report photo">
                                        </a>
                                        <button type="button" wire:click="removeExistingPhoto({{ $index }})"
                                            class="absolute top-2 right-2 bg-red-600 hover:bg-red-700 text-white rounded-full p-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Upload New Photos --}}
                    <div>
                        <label for="new_photos" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                            Add New Photos (Optional)
                        </label>
                        <input type="file" id="new_photos" wire:model="newPhotos" accept="image/*" multiple
                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 file:mr-4 file:rounded-md file:border-0 file:bg-blue-600 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-blue-700 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white dark:file:bg-blue-600 dark:hover:file:bg-blue-700 dark:focus:border-blue-500">
                        @error('newPhotos.*')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror

                        {{-- New Photo Preview --}}
                        @if (!empty($newPhotos))
                            <div class="mt-4 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                                @foreach ($newPhotos as $photo)
                                    <div class="relative">
                                        <img src="{{ $photo->temporaryUrl() }}"
                                            class="h-40 w-full rounded-lg object-cover border-2 border-green-500"
                                            alt="New photo">
                                        <span
                                            class="absolute top-2 left-2 bg-green-600 text-white text-xs px-2 py-1 rounded">
                                            New
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <div wire:loading wire:target="newPhotos" class="mt-2 text-sm text-blue-600 dark:text-blue-400">
                            Uploading photos...
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-6 border-t border-zinc-200 dark:border-zinc-700 flex justify-end space-x-3">
                <flux:button variant="ghost" type="button" wire:click="closeModal">
                    Cancel
                </flux:button>

                <flux:button variant="primary" type="submit" wire:loading.attr="disabled"
                    wire:target="updateReport, newPhotos">
                    <span wire:loading.remove wire:target="updateReport">Update Report</span>
                    <span wire:loading wire:target="updateReport">Updating...</span>
                </flux:button>
            </div>
        </form>
    </flux:modal>

    @if (session()->has('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
            class="fixed bottom-4 right-4 z-50 rounded-lg bg-green-50 p-4 shadow-lg dark:bg-green-900/20">
            <div class="flex items-center">
                <svg class="h-5 w-5 text-green-400 mr-3" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd" />
                </svg>
                <p class="text-sm font-medium text-green-800 dark:text-green-200">
                    {{ session('success') }}
                </p>
            </div>
        </div>
    @endif
</div>
