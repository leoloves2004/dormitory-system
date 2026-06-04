<aside class="app-sidebar fixed inset-y-0 left-0 z-40 flex w-72 -translate-x-full flex-col border-r border-slate-200 bg-white/95 shadow-xl backdrop-blur transition duration-200 lg:sticky lg:top-0 lg:h-screen lg:translate-x-0 lg:shadow-xs">
    <div class="border-b border-slate-200 px-5 py-5">
        <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('student.dashboard') }}" class="flex items-center gap-3">
            <span class="grid size-11 place-items-center rounded-lg bg-slate-900 text-sm font-bold text-white shadow-sm">DM</span>
            <span class="min-w-0">
                <span class="block text-base font-bold tracking-tight text-slate-950">Dormitory MS</span>
                <span class="block truncate text-xs font-medium text-slate-500">Campus housing control</span>
            </span>
        </a>
    </div>
    <x-navigation />
    <div class="border-t border-slate-200 p-4 lg:mt-auto">
        <div class="flex items-center justify-between gap-3 rounded-lg border border-slate-200 bg-slate-50 p-3 lg:block">
            <div class="min-w-0">
                <p class="truncate text-sm font-semibold">{{ auth()->user()->name }}</p>
                <p class="mt-1 text-xs capitalize text-slate-500">{{ auth()->user()->role }} account</p>
            </div>
            <div class="grid shrink-0 grid-cols-2 gap-2 lg:mt-3">
                <form method="post" action="{{ route('dark-mode') }}">@csrf<button class="btn-secondary w-full px-2 py-1.5">Theme</button></form>
                <form method="post" action="{{ route('logout') }}">@csrf<button class="btn-secondary w-full px-2 py-1.5">Logout</button></form>
            </div>
        </div>
    </div>
</aside>
