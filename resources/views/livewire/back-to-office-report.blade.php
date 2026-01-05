<div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Back to Office Report</h1>
    </div>

    @if (session()->has('success'))
        <div class="rounded-lg bg-green-50 p-4 dark:bg-green-900/20">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800 dark:text-green-200">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    <form wire:submit="submit" class="space-y-6">
        <!-- Travel Order ID -->
        <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-900">
            <label for="tracking_code" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                Travel Order ID <span class="text-red-500">*</span>
            </label>
            <div class="flex gap-2" x-data="{
                async viewPdf(trackingCode) {
                    if (!trackingCode) return;
            
                    try {
                        const pdfUrl = 'https://172.16.3.7/api/proxy/tracking/pdf/' + trackingCode;
                        const response = await fetch(pdfUrl);
                        const blob = await response.blob();
                        const url = URL.createObjectURL(blob);
                        window.open(url, '_blank');
                    } catch (err) {
                        console.error('Error loading PDF:', err);
                        alert('Failed to load PDF');
                    }
                }
            }">
                <input type="text" id="tracking_code" wire:model="tracking_code" placeholder="Enter Travel Order ID"
                    class="flex-1 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white dark:placeholder-gray-500 dark:focus:border-blue-500">
                <button type="button" wire:click="loadActivities" :disabled="!$wire.tracking_code"
                    class="rounded-lg bg-green-600 px-6 py-2.5 text-sm font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 disabled:opacity-50 disabled:cursor-not-allowed dark:bg-green-600 dark:hover:bg-green-700"
                    x-data :disabled="!$wire.tracking_code">
                    Load Activities
                </button>
                <button type="button" @click="viewPdf($wire.tracking_code)" :disabled="!$wire.tracking_code"
                    class="rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed dark:bg-blue-600 dark:hover:bg-blue-700"
                    x-data :disabled="!$wire.tracking_code">
                    View PDF
                </button>
            </div>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Enter the Travel Order ID used when enrolling
                activities</p>
        </div>

        @if (!empty($tracking_code) && count($reports) > 0)
            @foreach ($reports as $index => $report)
                <div
                    class="relative overflow-hidden rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-900">
                    <div class="mb-4 flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                            Report #{{ $index + 1 }}
                        </h2>
                        @if (count($reports) > 1)
                            <button type="button" wire:click="removeReport({{ $index }})"
                                class="rounded-lg border border-red-300 bg-white px-3 py-1.5 text-sm font-medium text-red-600 hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500 dark:border-red-600 dark:bg-neutral-800 dark:text-red-400 dark:hover:bg-neutral-700">
                                Remove
                            </button>
                        @endif
                    </div>

                    <div class="space-y-6">
                        <!-- Activity Name -->
                        <div>
                            <label for="activity_name_{{ $index }}"
                                class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                Activity Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="activity_name_{{ $index }}"
                                wire:model="reports.{{ $index }}.activity_name" placeholder="Enter activity name"
                                readonly
                                class="w-full rounded-lg border border-gray-300 bg-gray-50 px-4 py-2.5 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-neutral-600 dark:bg-neutral-700 dark:text-white dark:placeholder-gray-500 dark:focus:border-blue-500">
                            @error('reports.' . $index . '.activity_name')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Date of Travel -->
                        <div>
                            <label for="date_of_travel_{{ $index }}"
                                class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                Date of Travel <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="date_of_travel_{{ $index }}"
                                wire:model="reports.{{ $index }}.date_of_travel" readonly
                                class="w-full rounded-lg border border-gray-300 bg-gray-50 px-4 py-2.5 text-gray-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-neutral-600 dark:bg-neutral-700 dark:text-white dark:focus:border-blue-500">
                            @error('reports.' . $index . '.date_of_travel')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Purpose -->
                        <div>
                            <label for="purpose_{{ $index }}"
                                class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                Purpose <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="purpose_{{ $index }}"
                                wire:model="reports.{{ $index }}.purpose" placeholder="Purpose" readonly
                                class="w-full rounded-lg border border-gray-300 bg-gray-50 px-4 py-2.5 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-neutral-600 dark:bg-neutral-700 dark:text-white dark:placeholder-gray-500 dark:focus:border-blue-500">
                            @error('reports.' . $index . '.purpose')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Purpose Type -->
                        <div>
                            <label for="purpose_type_{{ $index }}"
                                class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                Purpose Type <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="purpose_type_{{ $index }}"
                                wire:model="reports.{{ $index }}.purpose_type" placeholder="Purpose Type"
                                readonly
                                class="w-full rounded-lg border border-gray-300 bg-gray-50 px-4 py-2.5 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-neutral-600 dark:bg-neutral-700 dark:text-white dark:placeholder-gray-500 dark:focus:border-blue-500">
                            @error('reports.' . $index . '.purpose_type')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Subproject Name (conditionally shown for Site Specific) -->
                        @if (!empty($report['purpose']) && $report['purpose'] === 'Site Specific')
                            <div>
                                <label for="subproject_name_{{ $index }}"
                                    class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                    Subproject Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="subproject_name_{{ $index }}"
                                    wire:model="reports.{{ $index }}.subproject_name"
                                    placeholder="Subproject Name" readonly
                                    class="w-full rounded-lg border border-gray-300 bg-gray-50 px-4 py-2.5 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-neutral-600 dark:bg-neutral-700 dark:text-white dark:placeholder-gray-500 dark:focus:border-blue-500">
                                @error('reports.' . $index . '.subproject_name')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif

                        <!-- Place -->
                        <div>
                            <label for="place_{{ $index }}"
                                class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                Place
                            </label>
                            <input type="text" id="place_{{ $index }}"
                                wire:model="reports.{{ $index }}.place" placeholder="Enter the location/place"
                                class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white dark:placeholder-gray-500 dark:focus:border-blue-500">
                            @error('reports.' . $index . '.place')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Accomplishment -->
                        <div>
                            <label for="accomplishment_{{ $index }}"
                                class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                Accomplishment
                            </label>
                            <textarea id="accomplishment_{{ $index }}" wire:model="reports.{{ $index }}.accomplishment"
                                rows="6" placeholder="Describe your accomplishments and activities during the travel"
                                class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white dark:placeholder-gray-500 dark:focus:border-blue-500"></textarea>
                            @error('reports.' . $index . '.accomplishment')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Geotagged Photos (One per Date) -->
                        @if (!empty($report['travel_dates']))
                            <div>
                                <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                    Geotagged Photos (One photo per day)
                                </label>
                                <p class="mb-4 text-sm text-gray-500 dark:text-gray-400">
                                    Upload one geotagged photo for each day of travel. You can upload new photos or select from your photo library.
                                </p>

                                <!-- Photo Selection Mode Toggle -->
                                <div class="mb-4 flex gap-2 rounded-lg bg-gray-100 p-1 dark:bg-neutral-800">
                                    <button type="button" wire:click="togglePhotoMode({{ $index }}, 'upload')"
                                        class="flex-1 rounded-md px-4 py-2 text-sm font-medium transition-colors {{ isset($photoSelectionMode[$index]) && $photoSelectionMode[$index] === 'upload' ? 'bg-white text-blue-600 shadow-sm dark:bg-neutral-700 dark:text-blue-400' : 'text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200' }}">
                                        Upload New Photos
                                    </button>
                                    <button type="button" wire:click="togglePhotoMode({{ $index }}, 'select')"
                                        class="flex-1 rounded-md px-4 py-2 text-sm font-medium transition-colors {{ isset($photoSelectionMode[$index]) && $photoSelectionMode[$index] === 'select' ? 'bg-white text-blue-600 shadow-sm dark:bg-neutral-700 dark:text-blue-400' : 'text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200' }}">
                                        Select from Library
                                    </button>
                                </div>

                                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                                    @foreach ($report['travel_dates'] as $dateKey => $date)
                                        @php
                                            $formattedDate = \Carbon\Carbon::parse($date)->format('F j, Y');
                                            $dayOfWeek = \Carbon\Carbon::parse($date)->format('l');
                                        @endphp
                                        
                                        <div class="rounded-lg border-2 border-gray-300 bg-gray-50 p-4 dark:border-neutral-600 dark:bg-neutral-800">
                                            <h3 class="mb-3 text-base font-semibold text-gray-900 dark:text-white">
                                                {{ $formattedDate }} <span class="text-sm font-normal text-gray-500 dark:text-gray-400">({{ $dayOfWeek }})</span>
                                            </h3>

                                            <!-- Upload New Photo Mode -->
                                            @if (!isset($photoSelectionMode[$index]) || $photoSelectionMode[$index] === 'upload')
                                                <div>
                                                    <input type="file" id="geotagged_photos_{{ $index }}_{{ $date }}"
                                                        wire:model="reports.{{ $index }}.geotagged_photos.{{ $date }}" 
                                                        accept="image/*"
                                                        class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 file:mr-4 file:rounded-md file:border-0 file:bg-blue-600 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-blue-700 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-neutral-600 dark:bg-neutral-700 dark:text-white dark:file:bg-blue-600 dark:hover:file:bg-blue-700 dark:focus:border-blue-500">
                                                    
                                                    @error('reports.' . $index . '.geotagged_photos.' . $date)
                                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                                    @enderror

                                                    <!-- Photo Preview -->
                                                    @if (!empty($report['geotagged_photos'][$date]))
                                                        <div class="mt-3">
                                                            <img src="{{ $report['geotagged_photos'][$date]->temporaryUrl() }}"
                                                                class="h-40 w-full rounded-lg object-cover">
                                                        </div>
                                                    @endif

                                                    <div wire:loading wire:target="reports.{{ $index }}.geotagged_photos.{{ $date }}"
                                                        class="mt-2 text-sm text-blue-600 dark:text-blue-400">
                                                        Uploading photo...
                                                    </div>
                                                </div>
                                            @endif

                                            <!-- Select from Library Mode -->
                                            @if (isset($photoSelectionMode[$index]) && $photoSelectionMode[$index] === 'select')
                                                <div>
                                                    @if (count($existingPhotos) > 0)
                                                        <p class="mb-3 text-sm text-gray-600 dark:text-gray-400">
                                                            Click on a photo to select it for this date. Only one photo per date.
                                                        </p>
                                                
                                                        <!-- Photo Grid for this specific date -->
                                                        <div class="grid grid-cols-3 gap-3 md:grid-cols-4">
                                                            @foreach ($existingPhotos as $existingPhoto)
                                                                @php
                                                                    $isSelected = !empty($report['selected_photo_ids'][$date]) && in_array($existingPhoto->id, $report['selected_photo_ids'][$date]);
                                                                @endphp
                                                                <div wire:click="togglePhotoSelection({{ $index }}, {{ $existingPhoto->id }}, '{{ $date }}')"
                                                                    class="group relative cursor-pointer overflow-hidden rounded-lg transition-all {{ $isSelected ? 'ring-4 ring-blue-500' : 'hover:ring-2 hover:ring-gray-400' }}">
                                                                    <img src="{{ \Storage::url($existingPhoto->photo_path) }}"
                                                                        class="h-24 w-full object-cover transition-transform group-hover:scale-105"
                                                                        loading="lazy">

                                                                    <!-- Selection Indicator -->
                                                                    @if ($isSelected)
                                                                        <div class="absolute inset-0 flex items-center justify-center bg-blue-600 bg-opacity-40">
                                                                            <svg class="h-8 w-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                                                <path fill-rule="evenodd"
                                                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                                                    clip-rule="evenodd" />
                                                                            </svg>
                                                                        </div>
                                                                    @endif

                                                                    <!-- Photo Info -->
                                                                    <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black to-transparent p-1">
                                                                        <p class="text-xs text-white truncate">
                                                                            {{ $existingPhoto->user->name ?? 'Unknown' }}
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>

                                                        <!-- Selected Photo Info -->
                                                        @if (!empty($report['selected_photo_ids'][$date]) && count($report['selected_photo_ids'][$date]) > 0)
                                                            @php
                                                                $selectedPhotoId = $report['selected_photo_ids'][$date][0];
                                                                $selectedPhoto = $existingPhotos->firstWhere('id', $selectedPhotoId);
                                                            @endphp
                                                            @if ($selectedPhoto)
                                                                <div class="mt-3 rounded-lg border border-blue-300 bg-blue-50 p-3 dark:border-blue-600 dark:bg-blue-900/20">
                                                                    <div class="flex items-center gap-3">
                                                                        <img src="{{ \Storage::url($selectedPhoto->photo_path) }}"
                                                                            class="h-16 w-16 rounded object-cover">
                                                                        <div>
                                                                            <p class="text-sm font-medium text-blue-900 dark:text-blue-100">
                                                                                Selected Photo
                                                                            </p>
                                                                            <p class="text-xs text-blue-700 dark:text-blue-300">
                                                                                By {{ $selectedPhoto->user->name ?? 'Unknown' }} on {{ $selectedPhoto->created_at->format('M d, Y') }}
                                                                            </p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        @endif
                                                    @else
                                                        <div class="rounded-lg bg-yellow-50 p-3 dark:bg-yellow-900/20">
                                                            <p class="text-sm text-yellow-800 dark:text-yellow-200">
                                                                No photos available. Switch to "Upload New Photos" to add a photo.
                                                            </p>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Monitoring Report (conditionally shown for Site Specific) -->
                        @if (!empty($report['purpose']) && $report['purpose'] === 'Site Specific')
                            <div>
                                <label for="monitoring_report_{{ $index }}"
                                    class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                    Monitoring Report
                                </label>
                                <input type="file" id="monitoring_report_{{ $index }}"
                                    wire:model="reports.{{ $index }}.monitoring_report"
                                    accept="application/pdf"
                                    class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 file:mr-4 file:rounded-md file:border-0 file:bg-blue-600 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-blue-700 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white dark:file:bg-blue-600 dark:hover:file:bg-blue-700 dark:focus:border-blue-500">
                                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Upload monitoring report in
                                    PDF format</p>

                                @error('reports.' . $index . '.monitoring_report')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror

                                <!-- File Name Preview -->
                                @if (!empty($report['monitoring_report']))
                                    <div class="mt-2 text-sm text-gray-700 dark:text-gray-300">
                                        Selected file: {{ $report['monitoring_report']->getClientOriginalName() }}
                                    </div>
                                @endif

                                <div wire:loading wire:target="reports.{{ $index }}.monitoring_report"
                                    class="mt-2 text-sm text-blue-600 dark:text-blue-400">
                                    Uploading PDF...
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        @elseif($loadAttempted && count($reports) === 0)
            <div class="rounded-lg bg-yellow-50 p-4 dark:bg-yellow-900/20">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">Travel Order ID
                            "{{ $tracking_code }}" has no enrolled activities</h3>
                        <p class="mt-1 text-sm text-yellow-700 dark:text-yellow-300">Please make sure you have enrolled
                            activities with this Travel Order ID first before creating a Back to Office Report.</p>
                    </div>
                </div>
            </div>
        @elseif(!$loadAttempted)
            <div class="rounded-lg bg-blue-50 p-4 dark:bg-blue-900/20">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-blue-800 dark:text-blue-200">Please enter your Travel Order
                            ID and click "Load Activities" to begin.</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Submit Button -->
        @if (!empty($tracking_code) && count($reports) > 0)
            <div class="flex justify-end gap-3 pt-4">
                <button type="button" wire:click="cancel"
                    class="rounded-lg border border-gray-300 bg-white px-6 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-neutral-600 dark:bg-neutral-800 dark:text-gray-300 dark:hover:bg-neutral-700">
                    Cancel
                </button>
                <button type="submit" wire:loading.attr="disabled" wire:target="submit"
                    class="rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 dark:bg-blue-600 dark:hover:bg-blue-700">
                    <span wire:loading.remove wire:target="submit">Submit
                        {{ count($reports) > 1 ? count($reports) . ' Reports' : 'Report' }}</span>
                    <span wire:loading wire:target="submit">Submitting...</span>
                </button>
            </div>
        @endif
    </form>
</div>
