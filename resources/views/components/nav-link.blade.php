@props(['active' => false])
<a {{ $attributes->class([
    'group flex items-center gap-3 rounded-md px-3 py-2.5 font-semibold transition',
    'bg-teal-700 text-white shadow-sm dark:bg-teal-500 dark:text-slate-950' => $active,
    'text-slate-600 hover:bg-slate-100 hover:text-slate-950 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white' => ! $active,
]) }}>{{ $slot }}</a>
