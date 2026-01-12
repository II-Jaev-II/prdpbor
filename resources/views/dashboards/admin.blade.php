<x-layouts.app :title="__('Admin Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Admin Dashboard</h1>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Welcome back, {{ auth()->user()->name }}! Manage your application from here.</p>
        </div>

        <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            <div class="relative overflow-hidden rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-900">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Users</p>
                        <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">0</p>
                    </div>
                    <div class="rounded-full bg-blue-100 p-3 dark:bg-blue-900">
                        <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="relative overflow-hidden rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-900">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Superiors</p>
                        <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">0</p>
                    </div>
                    <div class="rounded-full bg-green-100 p-3 dark:bg-green-900">
                        <svg class="h-6 w-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="relative overflow-hidden rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-900">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">System Status</p>
                        <p class="mt-2 text-xl font-semibold text-green-600 dark:text-green-400">Active</p>
                    </div>
                    <div class="rounded-full bg-purple-100 p-3 dark:bg-purple-900">
                        <svg class="h-6 w-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="relative h-full flex-1 overflow-hidden rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-900">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Admin Controls</h2>
            <div class="space-y-4">
                <p class="text-gray-600 dark:text-gray-400">You have full administrative access to the system.</p>
                <div class="grid gap-4 md:grid-cols-2">
                    <a href="{{ route('admin.pending-users') }}" class="flex items-center gap-3 rounded-lg border border-neutral-200 p-4 hover:bg-gray-50 dark:border-neutral-700 dark:hover:bg-neutral-800">
                        <div class="rounded-lg bg-orange-100 p-2 dark:bg-orange-900">
                            <svg class="h-5 w-5 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white">Pending Users</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Assign unit components</p>
                        </div>
                    </a>
                    <a href="#" class="flex items-center gap-3 rounded-lg border border-neutral-200 p-4 hover:bg-gray-50 dark:border-neutral-700 dark:hover:bg-neutral-800">
                        <div class="rounded-lg bg-blue-100 p-2 dark:bg-blue-900">
                            <svg class="h-5 w-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white">Manage Users</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">View and edit all users</p>
                        </div>
                    </a>
                    <a href="#" class="flex items-center gap-3 rounded-lg border border-neutral-200 p-4 hover:bg-gray-50 dark:border-neutral-700 dark:hover:bg-neutral-800">
                        <div class="rounded-lg bg-green-100 p-2 dark:bg-green-900">
                            <svg class="h-5 w-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white">System Settings</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Configure application</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
