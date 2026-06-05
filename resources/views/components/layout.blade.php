@props(['title' => 'Dormitory System'])
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>
    @php
        $viteManifestPath = public_path('build/manifest.json');
        $viteCssPath = null;

        if (file_exists($viteManifestPath)) {
            $viteManifest = json_decode(file_get_contents($viteManifestPath), true);
            $viteCssFile = $viteManifest['resources/css/app.css']['file'] ?? null;
            $viteCssPath = $viteCssFile ? public_path('build/'.$viteCssFile) : null;
        }
    @endphp
    @if ($viteCssPath && file_exists($viteCssPath))
        <style>{!! file_get_contents($viteCssPath) !!}</style>
    @endif
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="app-shell">
    @auth
        <div class="app-frame min-h-screen lg:flex">
            <x-sidebar />
            <button type="button" class="sidebar-backdrop fixed inset-0 z-30 hidden bg-slate-950/40 backdrop-blur-sm lg:hidden" data-sidebar-close aria-label="Close navigation drawer"></button>
            <main class="workspace-main">
                <header class="workspace-header">
                    <div class="mx-auto flex max-w-7xl items-center justify-between gap-4">
                        <div class="flex min-w-0 items-center gap-3">
                            <button type="button" class="icon-button" data-sidebar-toggle aria-label="Toggle navigation drawer" aria-expanded="false">
                                <span class="grid gap-1.5">
                                    <span class="block h-0.5 w-5 rounded-full bg-current"></span>
                                    <span class="block h-0.5 w-5 rounded-full bg-current"></span>
                                    <span class="block h-0.5 w-5 rounded-full bg-current"></span>
                                </span>
                            </button>
                            <div class="min-w-0">
                                <p class="eyebrow">{{ auth()->user()->role }} workspace</p>
                                <h1 class="mt-1 truncate text-xl font-bold tracking-tight text-slate-950 sm:text-2xl">{{ $title }}</h1>
                            </div>
                        </div>
                        <div class="hidden items-center gap-3 sm:flex">
                            <span class="rounded-full border border-slate-200 bg-white px-3 py-1.5 text-sm font-semibold text-slate-700 shadow-xs">{{ now()->format('M d, Y') }}</span>
                        </div>
                    </div>
                </header>
                <section class="content-shell">
                    @if(session('status'))<div class="mb-5 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800 shadow-xs">{{ session('status') }}</div>@endif
                    @if($errors->any())<div class="mb-5 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-800 shadow-xs">{{ $errors->first() }}</div>@endif
                    {{ $slot }}
                </section>
            </main>
        </div>
    @else
        <main class="grid min-h-screen place-items-center p-4 sm:p-6">{{ $slot }}</main>
    @endauth
</body>
</html>
