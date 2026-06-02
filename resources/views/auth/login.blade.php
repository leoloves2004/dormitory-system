<x-layout title="Login">
    <div class="grid w-full max-w-5xl overflow-hidden rounded-lg border border-slate-200 bg-white shadow-xl dark:border-slate-800 dark:bg-slate-900 lg:grid-cols-[1fr_420px]">
        <section class="hidden bg-slate-950 p-8 text-white lg:flex lg:flex-col lg:justify-between">
            <div>
                <span class="grid size-12 place-items-center rounded-lg bg-teal-400 text-sm font-bold text-slate-950">DM</span>
                <h1 class="mt-8 max-w-md text-4xl font-bold tracking-tight">Dormitory Management System</h1>
                <p class="mt-4 max-w-md text-sm leading-6 text-slate-300">A clean workspace for room assignments, tenant records, payments, applications, visitor logs, and reports.</p>
            </div>
            <div class="grid grid-cols-3 gap-3 text-sm">
                <div class="rounded-lg border border-white/10 bg-white/5 p-3">
                    <p class="font-bold text-teal-300">Rooms</p>
                    <p class="mt-1 text-slate-400">Availability tracking</p>
                </div>
                <div class="rounded-lg border border-white/10 bg-white/5 p-3">
                    <p class="font-bold text-teal-300">Tenants</p>
                    <p class="mt-1 text-slate-400">Resident records</p>
                </div>
                <div class="rounded-lg border border-white/10 bg-white/5 p-3">
                    <p class="font-bold text-teal-300">Reports</p>
                    <p class="mt-1 text-slate-400">Export-ready data</p>
                </div>
            </div>
        </section>
        <form method="post" action="{{ route('login') }}" class="p-6 sm:p-8">
            @csrf
            <div class="mb-6">
                <span class="grid size-12 place-items-center rounded-lg bg-teal-700 text-sm font-bold text-white lg:hidden">DM</span>
                <p class="eyebrow mt-4 lg:mt-0">Secure access</p>
                <h2 class="mt-2 text-2xl font-bold tracking-tight">Welcome back</h2>
                <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Sign in to continue managing dormitory operations.</p>
            </div>
            <label class="mb-3 block text-sm font-semibold">Email <input name="email" type="email" value="{{ old('email') }}" required class="field mt-1"></label>
            <label class="mb-4 block text-sm font-semibold">Password <input name="password" type="password" required class="field mt-1"></label>
            <label class="mb-4 flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300"><input name="remember" type="checkbox" class="rounded border-slate-300"> Remember me</label>
            <button class="btn-primary w-full">Login</button>
            <p class="mt-4 text-sm text-slate-500">New student? <a class="font-semibold text-teal-700 dark:text-teal-300" href="{{ route('register') }}">Create an account</a></p>
            <p class="mt-4 rounded-md border border-slate-200 bg-slate-50 px-3 py-2 text-xs text-slate-500 dark:border-slate-800 dark:bg-slate-950 dark:text-slate-400">Demo admin: admin@example.com / password</p>
        </form>
    </div>
</x-layout>
