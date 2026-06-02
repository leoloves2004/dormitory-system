<x-layout title="Admin Dashboard">
    @php
        $statMeta = [
            'total_rooms' => ['label' => 'Total rooms', 'hint' => 'All registered rooms'],
            'available_rooms' => ['label' => 'Available', 'hint' => 'Ready for assignment'],
            'occupied_rooms' => ['label' => 'Occupied', 'hint' => 'Currently assigned'],
            'total_students' => ['label' => 'Students', 'hint' => 'Profiles on record'],
            'total_tenants' => ['label' => 'Active tenants', 'hint' => 'Checked in residents'],
            'total_payments' => ['label' => 'Payments', 'hint' => 'Collected amount'],
            'pending_applications' => ['label' => 'Pending', 'hint' => 'Need review'],
            'approved_applications' => ['label' => 'Approved', 'hint' => 'Accepted requests'],
        ];
        $roomTotal = max(1, $stats['total_rooms']);
        $occupancyRate = round(($stats['occupied_rooms'] / $roomTotal) * 100);
    @endphp

    <section class="panel overflow-hidden">
        <div class="grid lg:grid-cols-[1.2fr_0.8fr]">
            <div class="p-6">
                <p class="eyebrow">Operations overview</p>
                <h2 class="mt-2 text-2xl font-bold tracking-tight">Housing activity at a glance</h2>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600 dark:text-slate-400">
                    Monitor occupancy, applications, student records, and payment collection from one administrative workspace.
                </p>
                <div class="mt-5 flex flex-wrap gap-2">
                    <a href="{{ route('admin.applications.index', ['status' => 'pending']) }}" class="btn-primary">Review applications</a>
                    <a href="{{ route('admin.reports.index') }}" class="btn-secondary">Generate reports</a>
                </div>
            </div>
            <div class="border-t border-slate-200 bg-slate-50 p-6 dark:border-slate-800 dark:bg-slate-950 lg:border-l lg:border-t-0">
                <p class="eyebrow">Occupancy rate</p>
                <div class="mt-5 flex items-end gap-3">
                    <span class="text-5xl font-bold tracking-tight">{{ $occupancyRate }}%</span>
                    <span class="pb-2 text-sm text-slate-500 dark:text-slate-400">{{ $stats['occupied_rooms'] }} of {{ $stats['total_rooms'] }} rooms</span>
                </div>
                <div class="mt-5 h-3 rounded-full bg-slate-200 dark:bg-slate-800">
                    <div class="h-3 rounded-full bg-teal-600" style="width: {{ $occupancyRate }}%"></div>
                </div>
            </div>
        </div>
    </section>

    <div class="mt-5 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        @foreach($stats as $label => $value)
            @php($meta = $statMeta[$label] ?? ['label' => str($label)->headline(), 'hint' => ''])
            <div class="panel p-5">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-sm font-semibold text-slate-600 dark:text-slate-300">{{ $meta['label'] }}</p>
                        <p class="mt-2 text-3xl font-bold tracking-tight">{{ is_numeric($value) ? number_format($value, str_contains($label, 'payments') ? 2 : 0) : $value }}</p>
                    </div>
                    <span class="grid size-10 place-items-center rounded-md bg-teal-50 text-sm font-bold text-teal-700 ring-1 ring-teal-100 dark:bg-teal-950 dark:text-teal-200 dark:ring-teal-900">{{ str($meta['label'])->substr(0, 1) }}</span>
                </div>
                <p class="mt-3 text-xs text-slate-500 dark:text-slate-400">{{ $meta['hint'] }}</p>
            </div>
        @endforeach
    </div>

    <div class="mt-5 grid gap-5 lg:grid-cols-2">
        <section class="panel">
            <div class="panel-header">
                <p class="eyebrow">Revenue</p>
                <h2 class="mt-1 text-lg font-bold">Payment analytics</h2>
            </div>
            <div class="panel-body space-y-4">
                @forelse($monthlyPayments as $month => $total)
                    <div>
                        <div class="mb-2 flex justify-between text-sm">
                            <span class="font-semibold">{{ $month }}</span>
                            <span class="text-slate-500 dark:text-slate-400">{{ number_format($total, 2) }}</span>
                        </div>
                        <div class="h-2 rounded-full bg-slate-200 dark:bg-slate-800">
                            <div class="h-2 rounded-full bg-teal-600" style="width: {{ min(100, $total / max(1, $monthlyPayments->max()) * 100) }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-slate-500 dark:text-slate-400">No payment data yet.</p>
                @endforelse
            </div>
        </section>
        <section class="panel">
            <div class="panel-header flex items-center justify-between gap-3">
                <div>
                    <p class="eyebrow">Queue</p>
                    <h2 class="mt-1 text-lg font-bold">Recent applications</h2>
                </div>
                <a href="{{ route('admin.applications.index') }}" class="text-sm font-semibold text-teal-700 dark:text-teal-300">View all</a>
            </div>
            <div class="divide-y divide-slate-200 dark:divide-slate-800">
                @forelse($recentApplications as $application)
                    <div class="flex items-center justify-between gap-4 px-5 py-4 text-sm">
                        <div>
                            <p class="font-semibold">{{ $application->student->user->name }}</p>
                            <p class="mt-1 text-slate-500 dark:text-slate-400">Requested {{ $application->preferredRoom?->room_number ?? 'any room' }}</p>
                        </div>
                        <span class="status-pill status-{{ $application->status }}">{{ $application->status }}</span>
                    </div>
                @empty
                    <p class="px-5 py-4 text-sm text-slate-500 dark:text-slate-400">No applications submitted yet.</p>
                @endforelse
            </div>
        </section>
    </div>
</x-layout>
