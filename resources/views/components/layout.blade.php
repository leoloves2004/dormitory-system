@props(['title' => 'Dormitory System'])
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="{{ auth()->user()?->dark_mode ? 'dark' : '' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="app-shell">
    @auth
        <div class="min-h-screen lg:flex">
            <aside class="flex flex-col border-r border-slate-200 bg-white/95 shadow-sm backdrop-blur dark:border-slate-800 dark:bg-slate-900/95 lg:sticky lg:top-0 lg:h-screen lg:w-72">
                <div class="border-b border-slate-200 px-5 py-5 dark:border-slate-800">
                    <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('student.dashboard') }}" class="flex items-center gap-3">
                        <span class="grid size-11 place-items-center rounded-lg bg-teal-700 text-sm font-bold text-white shadow-sm dark:bg-teal-500 dark:text-slate-950">DM</span>
                        <span>
                            <span class="block text-base font-bold tracking-tight text-slate-950 dark:text-white">Dormitory MS</span>
                            <span class="block text-xs font-medium text-slate-500 dark:text-slate-400">Campus housing control</span>
                        </span>
                    </a>
                </div>
                <nav class="grid gap-1.5 p-4 text-sm">
                    @if(auth()->user()->isAdmin())
                        <x-nav-link href="{{ route('admin.dashboard') }}" :active="request()->routeIs('admin.dashboard')">Dashboard</x-nav-link>
                        <x-nav-link href="{{ route('admin.rooms.index') }}" :active="request()->routeIs('admin.rooms.*')">Rooms</x-nav-link>
                        <x-nav-link href="{{ route('admin.students.index') }}" :active="request()->routeIs('admin.students.*')">Students</x-nav-link>
                        <x-nav-link href="{{ route('admin.tenants.index') }}" :active="request()->routeIs('admin.tenants.*')">Tenants</x-nav-link>
                        <x-nav-link href="{{ route('admin.applications.index') }}" :active="request()->routeIs('admin.applications.*')">Applications</x-nav-link>
                        <x-nav-link href="{{ route('admin.payments.index') }}" :active="request()->routeIs('admin.payments.*')">Payments</x-nav-link>
                        <x-nav-link href="{{ route('admin.visitor-logs.index') }}" :active="request()->routeIs('admin.visitor-logs.*')">Visitor Logs</x-nav-link>
                        <x-nav-link href="{{ route('admin.reports.index') }}" :active="request()->routeIs('admin.reports.*')">Reports</x-nav-link>
                    @else
                        <x-nav-link href="{{ route('student.dashboard') }}" :active="request()->routeIs('student.dashboard')">Student Portal</x-nav-link>
                    @endif
                </nav>
                <div class="mt-auto border-t border-slate-200 p-4 dark:border-slate-800">
                    <div class="rounded-lg border border-slate-200 bg-slate-50 p-3 dark:border-slate-800 dark:bg-slate-950">
                        <p class="text-sm font-semibold">{{ auth()->user()->name }}</p>
                        <p class="mt-1 text-xs capitalize text-slate-500 dark:text-slate-400">{{ auth()->user()->role }} account</p>
                        <div class="mt-3 grid grid-cols-2 gap-2">
                            <form method="post" action="{{ route('dark-mode') }}">@csrf<button class="btn-secondary w-full px-2 py-1.5">Theme</button></form>
                            <form method="post" action="{{ route('logout') }}">@csrf<button class="btn-secondary w-full px-2 py-1.5">Logout</button></form>
                        </div>
                    </div>
                </div>
            </aside>
            <main class="min-w-0 flex-1">
                <header class="sticky top-0 z-20 border-b border-slate-200 bg-white/85 px-5 py-4 shadow-xs backdrop-blur dark:border-slate-800 dark:bg-slate-900/85">
                    <div class="mx-auto flex max-w-7xl items-center justify-between gap-4">
                        <div>
                            <p class="eyebrow">{{ auth()->user()->role }} workspace</p>
                            <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-950 dark:text-white">{{ $title }}</h1>
                        </div>
                        <div class="hidden items-center gap-3 sm:flex">
                            <span class="rounded-full border border-slate-200 bg-white px-3 py-1.5 text-sm font-semibold text-slate-700 shadow-xs dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200">{{ now()->format('M d, Y') }}</span>
                        </div>
                    </div>
                </header>
                <section class="mx-auto max-w-7xl p-4 sm:p-5 lg:p-7">
                    @if(session('status'))<div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800 shadow-xs dark:border-emerald-900 dark:bg-emerald-950 dark:text-emerald-200">{{ session('status') }}</div>@endif
                    @if($errors->any())<div class="mb-4 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-800 shadow-xs dark:border-rose-900 dark:bg-rose-950 dark:text-rose-200">{{ $errors->first() }}</div>@endif
                    {{ $slot }}
                </section>
            </main>
        </div>
    @else
        <main class="grid min-h-screen place-items-center p-6">{{ $slot }}</main>
    @endauth
</body>
</html>
