<div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Back to Office Report</h1>
    </div>

    @if (session()->has('success'))
    <div class="rounded-lg bg-green-50 p-4 dark:bg-green-900/20">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-green-800 dark:text-green-200">{{ session('success') }}</p>
            </div>
        </div>
    </div>
    @endif

    <form wire:submit="submit" class="space-y-6">
        @foreach ($reports as $index => $report)
        <div class="relative overflow-hidden rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-900">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Report #{{ $index + 1 }}
                </h2>
                @if (count($reports) > 1)
                <button
                    type="button"
                    wire:click="removeReport({{ $index }})"
                    class="rounded-lg border border-red-300 bg-white px-3 py-1.5 text-sm font-medium text-red-600 hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500 dark:border-red-600 dark:bg-neutral-800 dark:text-red-400 dark:hover:bg-neutral-700">
                    Remove
                </button>
                @endif
            </div>

            <div class="space-y-6">
                <!-- Date of Travel -->
                <div>
                    <label for="date_of_travel_{{ $index }}" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                        Date of Travel <span class="text-red-500">*</span>
                    </label>
                    <div wire:ignore>
                        <input
                            type="text"
                            id="date_of_travel_{{ $index }}"
                            wire:model="reports.{{ $index }}.date_of_travel"
                            x-data
                            x-init="flatpickr($el, {
                                    mode: 'range',
                                    dateFormat: 'Y-m-d',
                                    enableTime: false,
                                    altInput: true,
                                    altFormat: 'F j, Y',
                                    onChange: function(selectedDates, dateStr) {
                                        $wire.set('reports.{{ $index }}.date_of_travel', dateStr);
                                    }
                                })"
                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white dark:focus:border-blue-500">
                    </div>
                    @error('reports.'.$index.'.date_of_travel')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Purpose -->
                <div>
                    <label for="purpose_{{ $index }}" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                        Purpose <span class="text-red-500">*</span>
                    </label>
                    <select
                        id="purpose_{{ $index }}"
                        wire:model="reports.{{ $index }}.purpose"
                        class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white dark:focus:border-blue-500">
                        <option value="">Select Purpose</option>
                        <option value="Meeting">Meeting</option>
                        <option value="Field Visit">Field Visit</option>
                        <option value="Training">Training</option>
                        <option value="Inspection">Inspection</option>
                        <option value="Conference">Conference</option>
                        <option value="Other">Other</option>
                    </select>
                    @error('reports.'.$index.'.purpose')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Place -->
                <div>
                    <label for="place_{{ $index }}" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                        Place <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        id="place_{{ $index }}"
                        wire:model="reports.{{ $index }}.place"
                        placeholder="Enter the location/place"
                        class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white dark:placeholder-gray-500 dark:focus:border-blue-500">
                    @error('reports.'.$index.'.place')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Accomplishment -->
                <div>
                    <label for="accomplishment_{{ $index }}" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                        Accomplishment <span class="text-red-500">*</span>
                    </label>
                    <textarea
                        id="accomplishment_{{ $index }}"
                        wire:model="reports.{{ $index }}.accomplishment"
                        rows="6"
                        placeholder="Describe your accomplishments and activities during the travel"
                        class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white dark:placeholder-gray-500 dark:focus:border-blue-500"></textarea>
                    @error('reports.'.$index.'.accomplishment')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Geotagged Photo -->
                <div>
                    <label for="geotagged_photos_{{ $index }}" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                        Geotagged Photo (at least 1) <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="file"
                        id="geotagged_photos_{{ $index }}"
                        wire:model="reports.{{ $index }}.geotagged_photos"
                        accept="image/*"
                        multiple
                        class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 file:mr-4 file:rounded-md file:border-0 file:bg-blue-600 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-blue-700 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white dark:file:bg-blue-600 dark:hover:file:bg-blue-700 dark:focus:border-blue-500">
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Upload one or more geotagged photos from your travel</p>

                    @error('reports.'.$index.'.geotagged_photos')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                    @error('reports.'.$index.'.geotagged_photos.*')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror

                    <!-- Photo Preview -->
                    @if (!empty($report['geotagged_photos']))
                    <div class="mt-4 grid grid-cols-2 gap-4 md:grid-cols-4">
                        @foreach ($report['geotagged_photos'] as $photo)
                        <div class="relative">
                            <img src="{{ $photo->temporaryUrl() }}" class="h-32 w-full rounded-lg object-cover">
                        </div>
                        @endforeach
                    </div>
                    @endif

                    <div wire:loading wire:target="reports.{{ $index }}.geotagged_photos" class="mt-2 text-sm text-blue-600 dark:text-blue-400">
                        Uploading photos...
                    </div>
                </div>
            </div>
        </div>
        @endforeach

        <!-- Add More Report Button -->
        <div class="flex justify-center">
            <button
                type="button"
                wire:click="addReport"
                class="rounded-lg border-2 border-dashed border-gray-300 bg-white px-6 py-3 text-sm font-medium text-gray-700 hover:border-blue-500 hover:bg-blue-50 hover:text-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-neutral-600 dark:bg-neutral-800 dark:text-gray-300 dark:hover:border-blue-500 dark:hover:bg-neutral-700 dark:hover:text-blue-400">
                <svg class="inline-block h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add Another Report
            </button>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end gap-3 pt-4">
            <button
                type="button"
                wire:click="cancel"
                class="rounded-lg border border-gray-300 bg-white px-6 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-neutral-600 dark:bg-neutral-800 dark:text-gray-300 dark:hover:bg-neutral-700">
                Cancel
            </button>
            <button
                type="submit"
                wire:loading.attr="disabled"
                wire:target="submit"
                class="rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 dark:bg-blue-600 dark:hover:bg-blue-700">
                <span wire:loading.remove wire:target="submit">Submit {{ count($reports) > 1 ? count($reports) . ' Reports' : 'Report' }}</span>
                <span wire:loading wire:target="submit">Submitting...</span>
            </button>
        </div>
    </form>
</div>