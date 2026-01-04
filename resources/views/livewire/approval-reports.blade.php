@php
    use Illuminate\Support\Facades\Storage;
@endphp

<div>
    <livewire:for-approval-table />

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
                                {{-- Travel Order ID --}}
                                <div>
                                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">
                                        Travel Order ID
                                    </label>
                                    <p class="text-base text-zinc-900 dark:text-white">
                                        {{ $report->travel_order_id ?? 'N/A' }}
                                    </p>
                                </div>

                                {{-- Activity Name --}}
                                @if ($report->enrollActivity)
                                    <div>
                                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">
                                            Activity Name
                                        </label>
                                        <p class="text-base text-zinc-900 dark:text-white">
                                            {{ $report->enrollActivity->activity_name ?? 'N/A' }}
                                        </p>
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
                                        {{ $report->purpose ?? 'N/A' }}
                                    </p>
                                </div>

                                {{-- Purpose Type --}}
                                @if ($report->enrollActivity && $report->enrollActivity->purpose_type)
                                    <div>
                                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">
                                            Purpose Type
                                        </label>
                                        <p class="text-base text-zinc-900 dark:text-white">
                                            {{ $report->enrollActivity->purpose_type }}
                                        </p>
                                    </div>
                                @endif

                                {{-- Subproject Name (conditionally shown for Site Specific) --}}
                                @if ($report->enrollActivity && $report->enrollActivity->subproject_name)
                                    <div>
                                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">
                                            Subproject Name
                                        </label>
                                        <p class="text-base text-zinc-900 dark:text-white">
                                            {{ $report->enrollActivity->subproject_name }}
                                        </p>
                                    </div>
                                @endif

                                {{-- Place --}}
                                @if ($report->place)
                                    <div>
                                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">
                                            Place
                                        </label>
                                        <p class="text-base text-zinc-900 dark:text-white">
                                            {{ $report->place }}
                                        </p>
                                    </div>
                                @endif

                                {{-- Accomplishment --}}
                                @if ($report->accomplishment)
                                    <div>
                                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">
                                            Accomplishment
                                        </label>
                                        <p class="text-base text-zinc-900 dark:text-white whitespace-pre-wrap">
                                            {{ $report->accomplishment }}
                                        </p>
                                    </div>
                                @endif

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

                                {{-- Geotagged Photos --}}
                                @if (!empty($report->photos) && count($report->photos) > 0)
                                    <div>
                                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-3">
                                            Geotagged Photos
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

                                {{-- Monitoring Report (conditionally shown for Site Specific) --}}
                                @if (!empty($report->monitoring_report))
                                    <div>
                                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                                            Monitoring Report (PDF)
                                        </label>
                                        <div
                                            class="flex items-center gap-3 p-4 rounded-lg border border-zinc-300 bg-zinc-50 dark:border-zinc-600 dark:bg-zinc-700">
                                            <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                            </svg>
                                            <div class="flex-1">
                                                <p class="text-sm font-medium text-zinc-900 dark:text-white">
                                                    {{ basename($report->monitoring_report) }}</p>
                                                <p class="text-xs text-zinc-500 dark:text-zinc-400">PDF Document</p>
                                            </div>
                                            <a href="{{ Storage::url($report->monitoring_report) }}" target="_blank"
                                                rel="noopener noreferrer"
                                                class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-blue-600 hover:text-blue-700 dark:text-blue-400">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                                View
                                            </a>
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
                @php
                    $hasPending = false;
                    foreach ($reports as $report) {
                        if ($report->status === 'Pending') {
                            $hasPending = true;
                            break;
                        }
                    }
                @endphp
                @if ($hasPending)
                    <button type="button" wire:click="openApprovalModal"
                        class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors">
                        Approve Report
                    </button>
                @endif
            </div>
        </div>
    </flux:modal>

    {{-- Approval ID Modal --}}
    <flux:modal name="approval-id-modal" wire:model="showApprovalModal" class="max-w-md">
        <form wire:submit="approveReport">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">Approve Report</flux:heading>
                    <flux:subheading>Enter a 4-character approval ID</flux:subheading>
                </div>

                <flux:input wire:model="approvalId" label="Approval ID" placeholder="e.g., A1B2" maxlength="4"
                    required autocomplete="off" class="uppercase" />
                @error('approvalId')
                    <flux:error>{{ $message }}</flux:error>
                @enderror

                <flux:subheading class="text-xs">
                    The approval ID must be exactly 4 alphanumeric characters (letters and numbers only).
                </flux:subheading>

                <div class="flex gap-2 justify-end">
                    <flux:button variant="ghost" type="button" wire:click="closeApprovalModal">
                        Cancel
                    </flux:button>
                    <flux:button variant="primary" type="submit">
                        Approve
                    </flux:button>
                </div>
            </div>
        </form>
    </flux:modal>
</div>
