@props(['active' => false])
<a {{ $attributes->class([
    'group flex items-center gap-3 rounded-md px-3.5 py-3 font-semibold transition',
    'bg-slate-900 text-white shadow-sm' => $active,
    'text-slate-600 hover:bg-slate-100 hover:text-slate-950' => ! $active,
]) }}>{{ $slot }}</a>
