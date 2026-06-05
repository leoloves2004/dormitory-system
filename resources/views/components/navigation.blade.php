<nav class="grid gap-1.5 overflow-y-auto p-4 text-sm">
    @if(auth()->user()->isAdmin())
        <x-nav-link href="{{ route('admin.dashboard') }}" :active="request()->routeIs('admin.dashboard')">Dashboard</x-nav-link>
        <x-nav-link href="{{ route('admin.rooms.index') }}" :active="request()->routeIs('admin.rooms.*')">Rooms</x-nav-link>
        <x-nav-link href="{{ route('admin.students.index') }}" :active="request()->routeIs('admin.students.*')">Students</x-nav-link>
        <x-nav-link href="{{ route('admin.tenants.index') }}" :active="request()->routeIs('admin.tenants.*')">Tenants</x-nav-link>
        <x-nav-link href="{{ route('admin.applications.index') }}" :active="request()->routeIs('admin.applications.*')">Applications</x-nav-link>
        <x-nav-link href="{{ route('admin.maintenance.index') }}" :active="request()->routeIs('admin.maintenance.*')">Maintenance</x-nav-link>
        <x-nav-link href="{{ route('admin.payments.index') }}" :active="request()->routeIs('admin.payments.*')">Payments</x-nav-link>
        <x-nav-link href="{{ route('admin.visitor-logs.index') }}" :active="request()->routeIs('admin.visitor-logs.*')">Visitor Logs</x-nav-link>
        <x-nav-link href="{{ route('admin.reports.index') }}" :active="request()->routeIs('admin.reports.*')">Reports</x-nav-link>
    @else
        <x-nav-link href="{{ route('student.dashboard') }}" :active="request()->routeIs('student.dashboard')">Student Portal</x-nav-link>
    @endif
</nav>
