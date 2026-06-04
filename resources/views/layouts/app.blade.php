@props(['title' => 'Dormitory System'])

<x-layout :title="$title">
    {{ $slot ?? '' }}
    @yield('content')
</x-layout>
