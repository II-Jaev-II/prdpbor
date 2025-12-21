@php
    use Illuminate\Support\Facades\Storage;
@endphp

<div>
    <livewire:approved-reports-table />

    {{-- View Reports Modal --}}
    <flux:modal name="view-reports-modal" wire:model="showViewModal" class="min-w-full min-h-screen m-0 rounded-none">
        <div class="h-full flex flex-col">
            <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                <flux:heading size="lg">Reports for {{ $currentReportNum }}</flux:heading>
                <flux:subheading>All reports under this report number</flux:subheading>
            </div>

            <div class="flex-1 overflow-y-auto p-6">
                <div class="max-w-7xl mx-auto space-y-6">
                    @foreach ($reports as $index => $report)
                        <div
                            class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 overflow-hidden">
                            <div
                                class="bg-zinc-50 dark:bg-zinc-900 px-6 py-4 border-b border-zinc-200 dark:border-zinc-700 flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">
                                        Report #{{ $index + 1 }}
                                    </h3>
                                    <p class="text-sm text-zinc-600 dark:text-zinc-400">
                                        Submitted by: {{ $report->user->name ?? 'Unknown' }}
                                    </p>
                                </div>
                                @if ($report->status === 'Approved')
                                    <div class="flex items-center gap-2">
                                        <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span
                                            class="text-sm font-medium text-green-600 dark:text-green-400">Approved</span>
                                    </div>
                                @endif
                            </div>

                            <div class="p-6 space-y-4">
                                {{-- Travel Order Information --}}
                                @if ($report->enrollActivity)
                                    <div
                                        class="bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800 p-4">
                                        <h4 class="text-sm font-semibold text-blue-900 dark:text-blue-100 mb-3">
                                            Travel Order #{{ $report->travel_order_id }}
                                        </h4>
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                            <div>
                                                <label
                                                    class="block text-xs font-medium text-blue-700 dark:text-blue-300 mb-1">
                                                    Activity Name
                                                </label>
                                                <p class="text-sm text-blue-900 dark:text-blue-100">
                                                    {{ $report->enrollActivity->activity_name ?? 'N/A' }}
                                                </p>
                                            </div>
                                            <div>
                                                <label
                                                    class="block text-xs font-medium text-blue-700 dark:text-blue-300 mb-1">
                                                    Purpose Type
                                                </label>
                                                <p class="text-sm text-blue-900 dark:text-blue-100">
                                                    {{ $report->enrollActivity->purpose_type ?? 'N/A' }}
                                                </p>
                                            </div>
                                            <div>
                                                <label
                                                    class="block text-xs font-medium text-blue-700 dark:text-blue-300 mb-1">
                                                    Subproject Name
                                                </label>
                                                <p class="text-sm text-blue-900 dark:text-blue-100">
                                                    {{ $report->enrollActivity->subproject_name ?? 'N/A' }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                {{-- Date Range --}}
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">
                                            Start Date
                                        </label>
                                        <p class="text-base text-zinc-900 dark:text-white">
                                            {{ $report->start_date ? $report->start_date->format('F j, Y') : 'N/A' }}
                                        </p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">
                                            End Date
                                        </label>
                                        <p class="text-base text-zinc-900 dark:text-white">
                                            {{ $report->end_date ? $report->end_date->format('F j, Y') : $report->start_date->format('F j, Y') }}
                                        </p>
                                    </div>
                                </div>

                                {{-- Purpose --}}
                                <div>
                                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">
                                        Purpose
                                    </label>
                                    <p class="text-base text-zinc-900 dark:text-white">
                                        {{ $report->purpose }}
                                    </p>
                                </div>

                                {{-- Place --}}
                                <div>
                                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">
                                        Place
                                    </label>
                                    <p class="text-base text-zinc-900 dark:text-white">
                                        {{ $report->place }}
                                    </p>
                                </div>

                                {{-- Accomplishment --}}
                                <div>
                                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">
                                        Accomplishment
                                    </label>
                                    <p class="text-base text-zinc-900 dark:text-white whitespace-pre-wrap">
                                        {{ $report->accomplishment }}
                                    </p>
                                </div>

                                {{-- Status --}}
                                <div>
                                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">
                                        Status
                                    </label>
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                        {{ $report->status === 'Pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400' : '' }}
                                        {{ $report->status === 'Approved' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : '' }}
                                        {{ $report->status === 'Rejected' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' : '' }}">
                                        {{ $report->status }}
                                    </span>
                                </div>

                                {{-- Approval ID (if approved) --}}
                                @if ($report->status === 'Approved' && $report->approval_id)
                                    <div>
                                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">
                                            Approval ID
                                        </label>
                                        <p class="text-base text-zinc-900 dark:text-white font-mono">
                                            {{ $report->approval_id }}
                                        </p>
                                    </div>
                                @endif

                                {{-- Photos --}}
                                @if (!empty($report->photos) && count($report->photos) > 0)
                                    <div>
                                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-3">
                                            Photos
                                        </label>
                                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                                            @foreach ($report->photos as $photo)
                                                <a href="{{ Storage::url($photo) }}" target="_blank"
                                                    rel="noopener noreferrer" class="block">
                                                    <img src="{{ Storage::url($photo) }}"
                                                        class="h-40 w-full rounded-lg object-cover border border-zinc-200 dark:border-zinc-700"
                                                        alt="Report photo">
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach

                    @if (count($reports) === 0)
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-zinc-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <p class="mt-4 text-zinc-600 dark:text-zinc-400">No reports found</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="p-6 border-t border-zinc-200 dark:border-zinc-700 flex justify-end gap-2">
                <button type="button" wire:click="generateReport"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                    Generate Report
                </button>
            </div>
        </div>
    </flux:modal>
</div>
