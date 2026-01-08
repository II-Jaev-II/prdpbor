<div>
    <x-slot name="header">
        <flux:heading size="xl">{{ __('Geotag Photos') }}</flux:heading>
        <flux:subheading>Upload and manage your geotagged photos</flux:subheading>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Success/Error Messages --}}
            @if (session('success'))
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
                            <p class="text-sm font-medium text-green-800 dark:text-green-200">{{ session('success') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 rounded-lg bg-red-50 dark:bg-red-900/20 p-4 border border-red-200 dark:border-red-800">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-red-800 dark:text-red-200">{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Search and Upload Section --}}
            <div class="mb-6 flex flex-col sm:flex-row gap-4 items-start sm:items-center justify-between">
                {{-- Search Bar --}}
                <div class="w-full sm:w-96">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" wire:model.live.debounce.300ms="search"
                            class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-300 bg-white text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white dark:placeholder-gray-500"
                            placeholder="Search by Travel Order ID, Activity, or User...">
                    </div>
                </div>

                {{-- Upload Button --}}
                <div>
                    <flux:button wire:click="openUploadModal" icon="photo">
                        Upload Geotag Photos
                    </flux:button>
                </div>
            </div>

            {{-- Photos Grid --}}
            @if ($groupedPhotos->count() > 0)
                <div class="space-y-8">
                    @foreach ($groupedPhotos as $group)
                        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-lg overflow-hidden">
                            {{-- Album Header --}}
                            <div class="bg-zinc-100 dark:bg-zinc-900 p-4 border-b border-zinc-200 dark:border-zinc-700">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-1">
                                            <flux:icon.document-text class="w-5 h-5 text-gray-600 dark:text-gray-400" />
                                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                                Travel Order: {{ $group['travel_order_id'] }}
                                            </h3>
                                        </div>
                                        @if ($group['enrolled_activity'])
                                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                                {{ $group['enrolled_activity']->activity_name }}
                                            </p>
                                        @endif
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $group['photo_count'] }}
                                            {{ Str::plural('photo', $group['photo_count']) }}
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            Latest: {{ $group['latest_upload']->format('M d, Y') }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Photos in Album --}}
                            <div class="p-4">
                                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                                    @foreach ($group['photos'] as $photo)
                                        <div class="relative group">
                                            {{-- Photo --}}
                                            <div
                                                class="aspect-square overflow-hidden rounded-lg bg-zinc-100 dark:bg-zinc-700">
                                                <img src="{{ $photo->photo_url }}" alt="Geotag Photo"
                                                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                                            </div>

                                            {{-- Photo Info Overlay --}}
                                            <div
                                                class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-2 rounded-b-lg opacity-0 group-hover:opacity-100 transition-opacity">
                                                <div class="space-y-1">
                                                    <div class="flex items-center justify-between text-white text-xs">
                                                        <div class="flex items-center gap-1">
                                                            <flux:icon.user class="w-3 h-3" />
                                                            <span class="truncate">{{ $photo->user->name }}</span>
                                                        </div>
                                                        <div class="flex items-center gap-1">
                                                            <button wire:click="downloadPhoto({{ $photo->id }})"
                                                                class="p-1 hover:bg-blue-600 rounded transition-colors">
                                                                <svg class="w-4 h-4" fill="currentColor"
                                                                    viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd"
                                                                        d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                                                                        clip-rule="evenodd" />
                                                                </svg>
                                                            </button>
                                                            @if ($photo->user_id === auth()->id())
                                                                <button wire:click="deletePhoto({{ $photo->id }})"
                                                                    wire:confirm="Are you sure you want to delete this photo?"
                                                                    class="p-1 hover:bg-red-600 rounded transition-colors">
                                                                    <svg class="w-4 h-4" fill="currentColor"
                                                                        viewBox="0 0 20 20">
                                                                        <path fill-rule="evenodd"
                                                                            d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                                            clip-rule="evenodd" />
                                                                    </svg>
                                                                </button>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="flex items-center gap-1 text-white text-xs">
                                                        <flux:icon.calendar class="w-3 h-3" />
                                                        <span>{{ $photo->created_at->format('M d, Y') }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <flux:icon.photo class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-600" />
                    <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">No photos</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by uploading your first
                        geotagged photo.</p>
                    <div class="mt-6">
                        <flux:button wire:click="openUploadModal" icon="photo">
                            Upload Geotag Photos
                        </flux:button>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Upload Modal --}}
    <flux:modal name="upload-photos-modal" wire:model="showModal" class="min-w-[600px]">
        <form wire:submit="uploadPhotos">
            <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                <flux:heading size="lg">Upload Geotag Photos</flux:heading>
                <flux:subheading>Upload photos for a specific Travel Order</flux:subheading>
            </div>

            <div class="p-6 space-y-6">
                {{-- Travel Order ID --}}
                <div>
                    <flux:input wire:model="travel_order_id" label="Travel Order ID *"
                        placeholder="Enter Travel Order ID" />
                    @error('travel_order_id')
                        <flux:error>{{ $message }}</flux:error>
                    @enderror
                </div>

                {{-- Photo Upload --}}
                <div>
                    <flux:label for="photos">Geotag Photos *</flux:label>
                    <input type="file" id="photos" wire:model="photos" multiple accept="image/*"
                        class="w-full mt-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white">
                    @error('photos.*')
                        <flux:error>{{ $message }}</flux:error>
                    @enderror

                    {{-- Photo Preview --}}
                    @if ($photos)
                        <div class="mt-4 grid grid-cols-3 gap-2">
                            @foreach ($photos as $photo)
                                <div
                                    class="aspect-square overflow-hidden rounded-lg border border-gray-300 dark:border-gray-600">
                                    <img src="{{ $photo->temporaryUrl() }}" class="w-full h-full object-cover">
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <div wire:loading wire:target="photos" class="mt-2 text-sm text-blue-600 dark:text-blue-400">
                        Uploading...
                    </div>
                </div>
            </div>

            <div class="flex gap-2 p-6 border-t border-zinc-200 dark:border-zinc-700">
                <flux:spacer />
                <flux:button type="button" variant="ghost" wire:click="closeModal">Cancel</flux:button>
                <flux:button type="submit" variant="primary">Upload</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
