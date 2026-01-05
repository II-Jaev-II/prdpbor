<div>
    <livewire:pending-table />

    {{-- Edit Modal --}}
    <flux:modal name="edit-report-modal" wire:model="showEditModal" class="min-w-full min-h-screen m-0 rounded-none">
        <form wire:submit="updateReport" class="h-full flex flex-col">
            <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                <flux:heading size="lg">Edit Report</flux:heading>
                <flux:subheading>Update the report details below</flux:subheading>
            </div>

            {{-- Superior Remarks Banner (if report was returned for revision) --}}
            @if ($superiorRemarks)
                <div class="mx-6 mt-6 bg-orange-50 dark:bg-orange-900/20 border-l-4 border-orange-500 p-4 rounded-r-lg">
                    <div class="flex items-start">
                        <svg class="w-6 h-6 text-orange-600 dark:text-orange-400 mt-0.5 mr-3 flex-shrink-0"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <div class="flex-1">
                            <h3 class="text-sm font-semibold text-orange-800 dark:text-orange-300 mb-2">
                                Report Returned for Revision
                            </h3>
                            <p class="text-sm text-orange-700 dark:text-orange-400 whitespace-pre-wrap mb-2">
                                {{ $superiorRemarks }}
                            </p>
                            @if ($returnedAt)
                                <p class="text-xs text-orange-600 dark:text-orange-500">
                                    Returned on {{ \Carbon\Carbon::parse($returnedAt)->format('F j, Y g:i A') }}
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <div class="flex-1 overflow-y-auto p-6">
                <div class="max-w-4xl mx-auto space-y-6">
                    {{-- Travel Order ID --}}
                    <div>
                        <label for="editForm.travel_order_id"
                            class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                            Travel Order ID <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="editForm.travel_order_id" wire:model="editForm.travel_order_id"
                            readonly
                            class="w-full rounded-lg border border-gray-300 bg-gray-50 px-4 py-2.5 text-gray-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-neutral-600 dark:bg-neutral-700 dark:text-white dark:focus:border-blue-500">
                    </div>

                    {{-- Activity Name --}}
                    <div>
                        <label for="editForm.activity_name"
                            class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                            Activity Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="editForm.activity_name" wire:model="editForm.activity_name" readonly
                            class="w-full rounded-lg border border-gray-300 bg-gray-50 px-4 py-2.5 text-gray-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-neutral-600 dark:bg-neutral-700 dark:text-white dark:focus:border-blue-500">
                    </div>

                    {{-- Date of Travel --}}
                    <div>
                        <label for="editForm.date_of_travel"
                            class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                            Date of Travel <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="editForm.date_of_travel" wire:model="editForm.date_of_travel" readonly
                            class="w-full rounded-lg border border-gray-300 bg-gray-50 px-4 py-2.5 text-gray-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-neutral-600 dark:bg-neutral-700 dark:text-white dark:focus:border-blue-500">
                    </div>

                    {{-- Purpose --}}
                    <div>
                        <label for="editForm.purpose"
                            class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                            Purpose <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="editForm.purpose" wire:model="editForm.purpose" readonly
                            class="w-full rounded-lg border border-gray-300 bg-gray-50 px-4 py-2.5 text-gray-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-neutral-600 dark:bg-neutral-700 dark:text-white dark:focus:border-blue-500">
                    </div>

                    {{-- Purpose Type --}}
                    @if (!empty($editForm['purpose_type']))
                        <div>
                            <label for="editForm.purpose_type"
                                class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                Purpose Type <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="editForm.purpose_type" wire:model="editForm.purpose_type" readonly
                                class="w-full rounded-lg border border-gray-300 bg-gray-50 px-4 py-2.5 text-gray-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-neutral-600 dark:bg-neutral-700 dark:text-white dark:focus:border-blue-500">
                        </div>
                    @endif

                    {{-- Subproject Name (conditionally shown for Site Specific) --}}
                    @if (!empty($editForm['subproject_name']))
                        <div>
                            <label for="editForm.subproject_name"
                                class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                Subproject Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="editForm.subproject_name" wire:model="editForm.subproject_name"
                                readonly
                                class="w-full rounded-lg border border-gray-300 bg-gray-50 px-4 py-2.5 text-gray-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-neutral-600 dark:bg-neutral-700 dark:text-white dark:focus:border-blue-500">
                        </div>
                    @endif

                    {{-- Place --}}
                    <div>
                        <label for="editForm.place"
                            class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                            Place
                        </label>
                        <input type="text" id="editForm.place" wire:model="editForm.place"
                            placeholder="Enter location/place"
                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white dark:placeholder-gray-500 dark:focus:border-blue-500">
                        @error('editForm.place')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Accomplishment --}}
                    <div>
                        <label for="editForm.accomplishment"
                            class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                            Accomplishment
                        </label>
                        <textarea id="editForm.accomplishment" wire:model="editForm.accomplishment" rows="6"
                            placeholder="Describe your accomplishments and activities during the travel"
                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white dark:placeholder-gray-500 dark:focus:border-blue-500"></textarea>
                        @error('editForm.accomplishment')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Existing Photos --}}
                    @if (!empty($existingPhotos))
                        <div>
                            <label class="block text-sm font-medium text-gray-900 dark:text-white mb-3">
                                Geotagged Photos
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
                            Add New Geotagged Photos (Optional)
                        </label>
                        <input type="file" id="new_photos" wire:model="newPhotos" accept="image/*" multiple
                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 file:mr-4 file:rounded-md file:border-0 file:bg-blue-600 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-blue-700 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white dark:file:bg-blue-600 dark:hover:file:bg-blue-700 dark:focus:border-blue-500">
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Upload one or more geotagged photos
                            from your travel</p>

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

                        <div wire:loading wire:target="newPhotos"
                            class="mt-2 text-sm text-blue-600 dark:text-blue-400">
                            Uploading photos...
                        </div>
                    </div>

                    {{-- Existing Monitoring Report (conditionally shown for Site Specific) --}}
                    @if (!empty($editForm['monitoring_report']))
                        <div>
                            <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                Monitoring Report (PDF)
                            </label>
                            <div
                                class="flex items-center gap-3 p-4 rounded-lg border border-gray-300 bg-gray-50 dark:border-neutral-600 dark:bg-neutral-700">
                                <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ basename($editForm['monitoring_report']) }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">PDF Document</p>
                                </div>
                                <div class="flex gap-2">
                                    <a href="{{ Storage::url($editForm['monitoring_report']) }}" target="_blank"
                                        rel="noopener noreferrer"
                                        class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-blue-600 hover:text-blue-700 dark:text-blue-400">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        View
                                    </a>
                                    <button type="button" wire:click="removeMonitoringReport"
                                        class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-red-600 hover:text-red-700 dark:text-red-400">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        Remove
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Upload New Monitoring Report (conditionally shown for Site Specific) --}}
                    @if (!empty($editForm['purpose']) && $editForm['purpose'] === 'Site Specific' && empty($editForm['monitoring_report']))
                        <div>
                            <label for="new_monitoring_report"
                                class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                Upload Monitoring Report (Optional)
                            </label>
                            <input type="file" id="new_monitoring_report" wire:model="newMonitoringReport"
                                accept="application/pdf"
                                class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 file:mr-4 file:rounded-md file:border-0 file:bg-blue-600 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-blue-700 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white dark:file:bg-blue-600 dark:hover:file:bg-blue-700 dark:focus:border-blue-500">
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Upload monitoring report in PDF
                                format</p>

                            @error('newMonitoringReport')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror

                            {{-- File Name Preview --}}
                            @if (!empty($newMonitoringReport))
                                <div class="mt-2 text-sm text-gray-700 dark:text-gray-300">
                                    Selected file: {{ $newMonitoringReport->getClientOriginalName() }}
                                </div>
                            @endif

                            <div wire:loading wire:target="newMonitoringReport"
                                class="mt-2 text-sm text-blue-600 dark:text-blue-400">
                                Uploading PDF...
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="p-6 border-t border-zinc-200 dark:border-zinc-700 flex justify-end space-x-3">
                <flux:button variant="ghost" type="button" wire:click="closeModal">
                    Cancel
                </flux:button>

                <flux:button variant="primary" type="submit" wire:loading.attr="disabled"
                    wire:target="updateReport, newPhotos, newMonitoringReport">
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
