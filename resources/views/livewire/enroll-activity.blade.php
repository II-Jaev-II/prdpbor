<div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Enroll Activity</h1>
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
        @foreach ($activities as $index => $activity)
            <div
                class="relative overflow-hidden rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-900">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                        Activity #{{ $index + 1 }}
                    </h2>
                    @if (count($activities) > 1)
                        <button type="button" wire:click="removeActivity({{ $index }})"
                            class="rounded-lg border border-red-300 bg-white px-3 py-1.5 text-sm font-medium text-red-600 hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500 dark:border-red-600 dark:bg-neutral-800 dark:text-red-400 dark:hover:bg-neutral-700">
                            Remove
                        </button>
                    @endif
                </div>

                <div class="space-y-6">
                    <!-- Travel Order Tracking Code -->
                    <div>
                        <label for="to_num_{{ $index }}"
                            class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                            Travel Order Tracking Code <span class="text-red-500">*</span>
                        </label>
                        <div x-data="{
                            trackingCodes: [],
                            loading: true,
                            error: null,
                            searchQuery: '',
                            showDropdown: false,
                            init() {
                                this.searchQuery = $wire.get('activities.{{ $index }}.to_num') || '';
                                this.fetchTrackingCodes();
                            },
                            get filteredCodes() {
                                if (!this.searchQuery) return this.trackingCodes;
                                return this.trackingCodes.filter(record =>
                                    record.TrackingCode.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                                    record.Destination.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                                    record.EmpName.toLowerCase().includes(this.searchQuery.toLowerCase())
                                );
                            },
                            async fetchTrackingCodes() {
                                try {
                                    this.loading = true;
                                    const response = await fetch('https://172.16.3.7/api/proxy/tracking/all');
                                    const data = await response.json();
                                    this.trackingCodes = Array.isArray(data) ? data : [data];
                                    this.loading = false;
                                } catch (err) {
                                    this.error = 'Failed to load tracking codes';
                                    this.loading = false;
                                    console.error('Error fetching tracking codes:', err);
                                }
                            },
                            selectCode(code) {
                                this.searchQuery = code;
                                this.showDropdown = false;
                                $wire.set('activities.{{ $index }}.to_num', code);
                        
                                // Find and set employee name(s)
                                const record = this.trackingCodes.find(r => r.TrackingCode === code);
                                if (record && record.EmpName) {
                                    // Split by common delimiters and clean up
                                    const names = record.EmpName.split(/[,;]/).map(name => name.trim()).filter(name => name.length > 0);
                                    $wire.set('activities.{{ $index }}.employee_name', names);
                                }
                            },
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
                        }" @click.away="showDropdown = false" class="relative">
                            <div class="flex gap-2">
                                <div class="relative flex-1">
                                    <input type="text" id="to_num_{{ $index }}" x-model="searchQuery"
                                        @focus="showDropdown = true"
                                        @input="showDropdown = true; $wire.set('activities.{{ $index }}.to_num', $event.target.value)"
                                        :disabled="loading"
                                        placeholder="Search tracking code, destination, or employee name..."
                                        class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white dark:placeholder-gray-500 dark:focus:border-blue-500">

                                    <!-- Dropdown -->
                                    <div x-show="showDropdown && !loading && !error" x-transition
                                        class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-lg border border-gray-300 bg-white shadow-lg dark:border-neutral-600 dark:bg-neutral-800">
                                        <template x-if="filteredCodes.length === 0">
                                            <div class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400">No matching
                                                tracking codes found</div>
                                        </template>
                                        <template x-for="record in filteredCodes" :key="record.TrackingCode">
                                            <div @click="selectCode(record.TrackingCode)"
                                                class="cursor-pointer px-4 py-2 hover:bg-blue-50 dark:hover:bg-neutral-700">
                                                <div class="font-medium text-gray-900 dark:text-white"
                                                    x-text="record.TrackingCode"></div>
                                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                                    <span x-text="record.Destination"></span> • <span
                                                        x-text="record.EmpName"></span>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                                <button type="button" @click="viewPdf(searchQuery)" :disabled="!searchQuery"
                                    class="rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed dark:bg-blue-600 dark:hover:bg-blue-700">
                                    View PDF
                                </button>
                            </div>
                        </div>
                        @error('activities.' . $index . '.to_num')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Name of Activity -->
                    <div>
                        <label for="activity_name_{{ $index }}"
                            class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                            Name of Activity <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="activity_name_{{ $index }}"
                            wire:model="activities.{{ $index }}.activity_name" placeholder="Enter activity name"
                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white dark:placeholder-gray-500 dark:focus:border-blue-500">
                        @error('activities.' . $index . '.activity_name')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Employee Names -->
                    <div>
                        <label for="employee_name_input_{{ $index }}"
                            class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                            Employee Names (Optional)
                        </label>
                        <div x-data="{
                            employeeNames: $wire.entangle('activities.{{ $index }}.employee_name'),
                            inputValue: '',
                            showDropdown: false,
                            registeredUsers: @js($users),
                            get filteredUsers() {
                                if (!this.inputValue) return this.registeredUsers;
                                const query = this.inputValue.toLowerCase();
                                return this.registeredUsers.filter(user => 
                                    user.name.toLowerCase().includes(query)
                                );
                            },
                            addName(name = null) {
                                const nameToAdd = name || this.inputValue.trim();
                                if (nameToAdd) {
                                    if (!Array.isArray(this.employeeNames)) {
                                        this.employeeNames = [];
                                    }
                                    // Avoid duplicates
                                    if (!this.employeeNames.includes(nameToAdd)) {
                                        this.employeeNames.push(nameToAdd);
                                    }
                                    this.inputValue = '';
                                    this.showDropdown = false;
                                }
                            },
                            selectUser(userName) {
                                this.addName(userName);
                            },
                            removeName(index) {
                                this.employeeNames.splice(index, 1);
                            }
                        }" @click.away="showDropdown = false">
                            <!-- Display current names as tags -->
                            <div class="flex flex-wrap gap-2 mb-2" x-show="employeeNames && employeeNames.length > 0">
                                <template x-for="(name, idx) in employeeNames" :key="idx">
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-blue-100 px-3 py-1 text-sm font-medium text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                                        <span x-text="name"></span>
                                        <button type="button" @click="removeName(idx)" class="hover:text-blue-600 dark:hover:text-blue-200">
                                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                            </svg>
                                        </button>
                                    </span>
                                </template>
                            </div>
                            
                            <!-- Input to add new names with dropdown -->
                            <div class="relative">
                                <div class="flex gap-2">
                                    <input type="text" id="employee_name_input_{{ $index }}"
                                        x-model="inputValue"
                                        @focus="showDropdown = true"
                                        @input="showDropdown = true"
                                        @keydown.enter.prevent="addName()"
                                        @keydown.arrow-down.prevent="$refs.dropdown?.querySelector('div')?.focus()"
                                        autocomplete="off"
                                        placeholder="Type to search or select from registered users..."
                                        class="flex-1 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white dark:placeholder-gray-500 dark:focus:border-blue-500">
                                    <button type="button" @click="addName()"
                                        class="rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-blue-600 dark:hover:bg-blue-700">
                                        Add
                                    </button>
                                </div>
                                
                                <!-- Dropdown of registered users -->
                                <div x-show="showDropdown && filteredUsers.length > 0" x-ref="dropdown" x-transition
                                    class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-lg border border-gray-300 bg-white shadow-lg dark:border-neutral-600 dark:bg-neutral-800">
                                    <template x-for="user in filteredUsers" :key="user.id">
                                        <div @click="selectUser(user.name)"
                                            class="cursor-pointer px-4 py-2.5 hover:bg-blue-50 dark:hover:bg-neutral-700">
                                            <div class="flex items-center">
                                                <svg class="h-5 w-5 text-gray-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                                </svg>
                                                <span class="font-medium text-gray-900 dark:text-white" x-text="user.name"></span>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                Select from registered users or type a custom name. This helps in searching when creating Back to Office Reports.
                            </p>
                        </div>
                    </div>

                    <!-- Component -->
                    <div>
                        <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                            Component <span class="text-red-500">*</span>
                        </label>
                        <div class="space-y-2">
                            @foreach (['IBUILD', 'IREAP', 'IPLAN', 'ISUPPORT'] as $component)
                                <label
                                    class="flex items-center p-3 rounded-lg border border-gray-300 bg-white hover:bg-gray-50 cursor-pointer dark:border-neutral-600 dark:bg-neutral-800 dark:hover:bg-neutral-700">
                                    <input type="checkbox" wire:model="activities.{{ $index }}.unit_component"
                                        value="{{ $component }}"
                                        class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-2 focus:ring-blue-500 dark:border-neutral-600 dark:bg-neutral-700 dark:ring-offset-neutral-800">
                                    <span
                                        class="ml-3 text-sm font-medium text-gray-900 dark:text-white">{{ $component }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('activities.' . $index . '.unit_component')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Purpose -->
                    <div>
                        <label for="purpose_{{ $index }}"
                            class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                            Purpose <span class="text-red-500">*</span>
                        </label>
                        <select id="purpose_{{ $index }}"
                            wire:model.live="activities.{{ $index }}.purpose"
                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white dark:focus:border-blue-500">
                            <option value="">Select Purpose</option>
                            <option value="Site Specific">Site Specific</option>
                            <option value="Non Site Specific">Non Site Specific</option>
                        </select>
                        @error('activities.' . $index . '.purpose')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Purpose Type (conditionally shown) -->
                    @if (!empty($activity['purpose']))
                        <div>
                            <label for="purpose_type_{{ $index }}"
                                class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                Purpose Type <span class="text-red-500">*</span>
                            </label>
                            <select id="purpose_type_{{ $index }}"
                                wire:model.live="activities.{{ $index }}.purpose_type"
                                class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white dark:focus:border-blue-500">
                                <option value="">Select Purpose Type</option>
                                @if ($activity['purpose'] == 'Site Specific')
                                    <option value="JIT">JIT</option>
                                    <option value="Validation">Validation</option>
                                @elseif($activity['purpose'] == 'Non Site Specific')
                                    <option value="Assessment">Assessment</option>
                                    <option value="Meeting">Meeting</option>
                                    <option value="Training">Training</option>
                                @endif
                            </select>
                            @error('activities.' . $index . '.purpose_type')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif

                    <!-- Subproject List (conditionally shown for Site Specific) -->
                    @if (
                        !empty($activity['purpose_type']) &&
                            $activity['purpose_type'] !== 'Validation' &&
                            $activity['purpose'] === 'Site Specific')
                        <div>
                            <label for="subproject_id_{{ $index }}"
                                class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                Subproject <span class="text-red-500">*</span>
                            </label>
                            <select id="subproject_id_{{ $index }}"
                                wire:model="activities.{{ $index }}.subproject_id"
                                class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white dark:focus:border-blue-500">
                                <option value="">Select Subproject</option>
                                @foreach ($subprojects as $subproject)
                                    <option value="{{ $subproject->id }}">{{ $subproject->subproject_name }}</option>
                                @endforeach
                            </select>
                            @error('activities.' . $index . '.subproject_id')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    @elseif(!empty($activity['purpose_type']) && $activity['purpose_type'] == 'Validation')
                        <!-- Show a textbox instead of dropdown of subproject list -->
                        <div>
                            <label for="subproject_name_{{ $index }}"
                                class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                Subproject Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="subproject_name_{{ $index }}"
                                wire:model="activities.{{ $index }}.subproject_name"
                                placeholder="Enter subproject name"
                                class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white dark:placeholder-gray-500 dark:focus:border-blue-500">
                            @error('activities.' . $index . '.subproject_name')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif

                    <!-- Duration of Travel -->
                    <div>
                        <label for="travel_duration_{{ $index }}"
                            class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                            Duration of Travel <span class="text-red-500">*</span>
                        </label>
                        <div wire:ignore>
                            <input type="text" id="travel_duration_{{ $index }}"
                                wire:model="activities.{{ $index }}.travel_duration" x-data
                                x-init="flatpickr($el, {
                                    mode: 'range',
                                    dateFormat: 'Y-m-d',
                                    enableTime: false,
                                    altInput: true,
                                    altFormat: 'F j, Y',
                                    onChange: function(selectedDates, dateStr) {
                                        $wire.set('activities.{{ $index }}.travel_duration', dateStr);
                                    }
                                })"
                                class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white dark:focus:border-blue-500">
                        </div>
                        @error('activities.' . $index . '.travel_duration')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        @endforeach

        <!-- Add More Activity Button -->
        <div class="flex justify-center">
            <button type="button" wire:click="addActivity"
                class="rounded-lg border-2 border-dashed border-gray-300 bg-white px-6 py-3 text-sm font-medium text-gray-700 hover:border-blue-500 hover:bg-blue-50 hover:text-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-neutral-600 dark:bg-neutral-800 dark:text-gray-300 dark:hover:border-blue-500 dark:hover:bg-neutral-700 dark:hover:text-blue-400">
                <svg class="inline-block h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add Another Activity
            </button>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end gap-3 pt-4">
            <button type="button" wire:click="cancel"
                class="rounded-lg border border-gray-300 bg-white px-6 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-neutral-600 dark:bg-neutral-800 dark:text-gray-300 dark:hover:bg-neutral-700">
                Cancel
            </button>
            <button type="submit" wire:loading.attr="disabled" wire:target="submit"
                class="rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 dark:bg-blue-600 dark:hover:bg-blue-700">
                <span wire:loading.remove wire:target="submit">Submit
                    {{ count($activities) > 1 ? count($activities) . ' Activities' : 'Activity' }}</span>
                <span wire:loading wire:target="submit">Submitting...</span>
            </button>
        </div>
    </form>
</div>
