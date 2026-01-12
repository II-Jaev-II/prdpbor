<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Back to Office Report</title>

    <link rel="icon" href="/prdp-logo.png" type="image/png">
    <link rel="apple-touch-icon" href="/prdp-logo.png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600&display=swap" rel="stylesheet" />

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] flex p-6 lg:p-8 items-center lg:justify-center min-h-screen flex-col">
    <header class="w-full lg:max-w-4xl max-w-[335px] text-sm mb-6 not-has-[nav]:hidden">
        @if (Route::has('login'))
        <nav class="flex items-center justify-end gap-4">
            @auth
            <a
                href="{{ url('/dashboard') }}"
                class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] border-[#19140035] hover:border-[#1915014a] border text-[#1b1b18] dark:border-[#3E3E3A] dark:hover:border-[#62605b] rounded-sm text-sm leading-normal">
                Dashboard
            </a>
            @else
            <a
                href="{{ route('login') }}"
                class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] text-[#1b1b18] border border-transparent hover:border-[#19140035] dark:hover:border-[#3E3E3A] rounded-sm text-sm leading-normal">
                Log in
            </a>

            @if (Route::has('register'))
            <a
                href="{{ route('register') }}"
                class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] border-[#19140035] hover:border-[#1915014a] border text-[#1b1b18] dark:border-[#3E3E3A] dark:hover:border-[#62605b] rounded-sm text-sm leading-normal">
                Register
            </a>
            @endif
            @endauth
        </nav>
        @endif
    </header>

    @if (Route::has('login'))
    <div class="h-14.5 hidden lg:block"></div>
    @endif

    <main class="w-full lg:max-w-4xl max-w-[335px] flex flex-col items-center justify-center flex-1">
        <div class="flex flex-row w-48 items-center justify-center gap-4 mb-8">
            <img src="/rpco-logo.png" alt="PRDP Logo"/>
            <img src="/BP-logo.png" alt="Bagong Pilipinas Logo"/>
        </div>
        <div class="text-center mb-8">
            <h1 class="text-4xl lg:text-6xl font-bold mb-4 dark:text-[#EDEDEC]">Back to Office Report</h1>
            <p class="text-lg lg:text-xl text-gray-600 dark:text-gray-400">Streamlined reporting and activity tracking system</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 w-full mt-8">
            <div class="p-6 border border-[#19140035] dark:border-[#3E3E3A] rounded-lg hover:border-[#1915014a] dark:hover:border-[#62605b] transition">
                <h2 class="text-xl font-semibold mb-3 dark:text-[#EDEDEC]">Back to Office Reports</h2>
                <p class="text-gray-600 dark:text-gray-400">Submit and manage your back-to-office activity reports efficiently.</p>
            </div>

            <div class="p-6 border border-[#19140035] dark:border-[#3E3E3A] rounded-lg hover:border-[#1915014a] dark:hover:border-[#62605b] transition">
                <h2 class="text-xl font-semibold mb-3 dark:text-[#EDEDEC]">Enroll Activities</h2>
                <p class="text-gray-600 dark:text-gray-400">Track and enroll your field activities with complete documentation.</p>
            </div>

            <div class="p-6 border border-[#19140035] dark:border-[#3E3E3A] rounded-lg hover:border-[#1915014a] dark:hover:border-[#62605b] transition">
                <h2 class="text-xl font-semibold mb-3 dark:text-[#EDEDEC]">Geotag Photos</h2>
                <p class="text-gray-600 dark:text-gray-400">Capture and store location-tagged photos for verification purposes.</p>
            </div>

            <div class="p-6 border border-[#19140035] dark:border-[#3E3E3A] rounded-lg hover:border-[#1915014a] dark:hover:border-[#62605b] transition">
                <h2 class="text-xl font-semibold mb-3 dark:text-[#EDEDEC]">Approval System</h2>
                <p class="text-gray-600 dark:text-gray-400">Comprehensive approval workflow for all submitted reports.</p>
            </div>
        </div>
    </main>
</body>

</html>