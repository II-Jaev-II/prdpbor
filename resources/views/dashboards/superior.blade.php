<x-layouts.app :title="__('Superior Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Superior Dashboard</h1>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Welcome back, {{ auth()->user()->name }}! Manage your team from here.</p>
        </div>

        <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            <div class="relative overflow-hidden rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-900">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">My Team</p>
                        <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">0</p>
                    </div>
                    <div class="rounded-full bg-indigo-100 p-3 dark:bg-indigo-900">
                        <svg class="h-6 w-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="relative overflow-hidden rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-900">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Active Tasks</p>
                        <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">0</p>
                    </div>
                    <div class="rounded-full bg-amber-100 p-3 dark:bg-amber-900">
                        <svg class="h-6 w-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="relative overflow-hidden rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-900">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Performance</p>
                        <p class="mt-2 text-xl font-semibold text-green-600 dark:text-green-400">Excellent</p>
                    </div>
                    <div class="rounded-full bg-green-100 p-3 dark:bg-green-900">
                        <svg class="h-6 w-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="relative h-full flex-1 overflow-hidden rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-900">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Team Management</h2>
            <div class="space-y-4">
                <p class="text-gray-600 dark:text-gray-400">Manage your team members and track their activities.</p>
                <div class="grid gap-4 md:grid-cols-2">
                    <a href="#" class="flex items-center gap-3 rounded-lg border border-neutral-200 p-4 hover:bg-gray-50 dark:border-neutral-700 dark:hover:bg-neutral-800">
                        <div class="rounded-lg bg-indigo-100 p-2 dark:bg-indigo-900">
                            <svg class="h-5 w-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white">Team Members</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">View your team</p>
                        </div>
                    </a>
                    <a href="#" class="flex items-center gap-3 rounded-lg border border-neutral-200 p-4 hover:bg-gray-50 dark:border-neutral-700 dark:hover:bg-neutral-800">
                        <div class="rounded-lg bg-amber-100 p-2 dark:bg-amber-900">
                            <svg class="h-5 w-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white">Reports</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">View performance reports</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
