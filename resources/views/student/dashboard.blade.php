<x-layout :title="$title ?? 'Student Portal'">
    @php($section = $section ?? 'dashboard')

    @if($section === 'maintenance')
        <div class="grid gap-5 sm:gap-6 lg:grid-cols-[1.2fr_0.8fr]">
            <section class="panel">
                <div class="panel-header">
                    <p class="eyebrow">Maintenance request</p>
                    <h2 class="mt-1 text-lg font-bold">Request service</h2>
                </div>

                <form method="post" action="{{ route('student.maintenance.store') }}" class="panel-body grid gap-3">
                    @csrf

                    <input name="title" value="{{ old('title') }}" placeholder="Issue title" required class="field">

                    <select name="priority" required class="field">
                        @foreach(['Low', 'Medium', 'High'] as $priority)
                            <option value="{{ $priority }}" @selected(old('priority', 'Low') === $priority)>
                                {{ $priority }} priority
                            </option>
                        @endforeach
                    </select>

                    <textarea name="description" placeholder="Describe the problem, location, and any useful details" required class="field min-h-32">{{ old('description') }}</textarea>

                    <button class="btn-primary">Submit maintenance request</button>
                </form>
            </section>

            <section class="panel">
                <div class="panel-header">
                    <p class="eyebrow">Request history</p>
                    <h2 class="mt-1 text-lg font-bold">Maintenance updates</h2>
                </div>

                <div class="divide-y divide-slate-200 dark:divide-slate-800">
                    @forelse($maintenanceRequests as $request)
                        <div class="p-5 text-sm">
                            <div class="flex items-center justify-between gap-3">
                                <strong>{{ $request->title }}</strong>
                                <span class="status-pill status-{{ str_replace(' ', '_', strtolower($request->status)) }}">
                                    {{ $request->status }}
                                </span>
                            </div>
                            <p class="mt-2 text-slate-500 dark:text-slate-400">
                                {{ $request->description }}
                            </p>
                            <p class="mt-3 text-xs font-semibold text-slate-500">
                                {{ $request->priority }} priority - {{ optional($request->created_at)->diffForHumans() }}
                            </p>
                        </div>
                    @empty
                        <p class="p-5 text-sm text-slate-500 dark:text-slate-400">
                            No maintenance requests submitted yet.
                        </p>
                    @endforelse
                </div>
            </section>
        </div>
    @elseif($section === 'housing')
        <div class="grid gap-5 sm:gap-6 lg:grid-cols-3">
            <section class="panel p-5 sm:p-6">
                <p class="eyebrow">Current assigned room</p>
                <p class="mt-3 text-3xl font-bold tracking-tight sm:text-4xl">
                    {{ $student->room?->room_number ?? 'Unassigned' }}
                </p>
                <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">
                    {{ $student->room?->building }}
                    {{ $student->room ? 'Floor '.$student->room->floor : 'Submit an application to request a room.' }}
                </p>

                @if($student->room)
                    <div class="mt-5 rounded-lg border border-slate-200 bg-slate-50 p-4 text-center dark:border-slate-800 dark:bg-slate-950">
                        <p class="eyebrow">QR access code</p>
                        <p class="mt-2 break-all font-mono text-sm font-semibold">
                            {{ $student->room->qr_code }}
                        </p>
                    </div>
                @endif
            </section>

            <section class="panel lg:col-span-2">
                <div class="panel-header">
                    <p class="eyebrow">Housing request</p>
                    <h2 class="mt-1 text-lg font-bold">Apply for a room</h2>
                </div>

                <form method="post" action="{{ route('student.apply') }}" class="panel-body grid gap-3 md:grid-cols-3">
                    @csrf

                    <select name="room_id" class="field">
                        <option value="">Any available room</option>
                        @foreach($rooms as $room)
                            <option value="{{ $room->id }}">
                                {{ $room->room_number }} - {{ number_format($room->monthly_fee, 2) }}
                            </option>
                        @endforeach
                    </select>

                    <input name="application_date" type="date" value="{{ now()->toDateString() }}" class="field">

                    <input name="reason" placeholder="Reason for request" required class="field">

                    <button class="btn-primary md:col-span-3">Submit application</button>
                </form>
            </section>
        </div>

        <section class="panel mt-6">
            <div class="panel-header">
                <p class="eyebrow">Status feed</p>
                <h2 class="mt-1 text-lg font-bold">Application updates</h2>
            </div>

            <div class="divide-y divide-slate-200 dark:divide-slate-800">
                @forelse($student->roomApplications->sortByDesc('created_at') as $application)
                    <div class="p-5 text-sm">
                        <div class="flex items-center justify-between gap-3">
                            <strong>{{ $application->preferredRoom?->room_number ?? 'Any room' }}</strong>
                            <span class="status-pill status-{{ $application->status }}">
                                {{ $application->status }}
                            </span>
                        </div>
                        <p class="mt-2 text-slate-500 dark:text-slate-400">
                            {{ $application->remarks ?: $application->reason }}
                        </p>
                    </div>
                @empty
                    <p class="p-5 text-sm text-slate-500 dark:text-slate-400">
                        No applications submitted yet.
                    </p>
                @endforelse
            </div>
        </section>
    @else
        <div class="grid gap-5 sm:gap-6 lg:grid-cols-3">
            <section class="panel p-5 sm:p-6">
                <p class="eyebrow">Current assigned room</p>
                <p class="mt-3 text-3xl font-bold tracking-tight sm:text-4xl">
                    {{ $student->room?->room_number ?? 'Unassigned' }}
                </p>
                <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">
                    {{ $student->room?->building }}
                    {{ $student->room ? 'Floor '.$student->room->floor : 'Submit a housing request to request a room.' }}
                </p>

                @if($student->room)
                    <div class="mt-5 rounded-lg border border-slate-200 bg-slate-50 p-4 text-center dark:border-slate-800 dark:bg-slate-950">
                        <p class="eyebrow">QR access code</p>
                        <p class="mt-2 break-all font-mono text-sm font-semibold">
                            {{ $student->room->qr_code }}
                        </p>
                    </div>
                @endif
            </section>

            <section class="panel lg:col-span-2">
                <div class="panel-header">
                    <p class="eyebrow">Personal details</p>
                    <h2 class="mt-1 text-lg font-bold">Profile</h2>
                </div>

                <form method="post" action="{{ route('student.profile') }}" class="panel-body grid gap-3 md:grid-cols-2">
                    @csrf
                    @method('put')

                    <input name="course" value="{{ $student->course }}" placeholder="Course" class="field">
                    <input name="year_level" value="{{ $student->year_level }}" placeholder="Year level" class="field">
                    <input name="contact_number" value="{{ $student->contact_number }}" placeholder="Contact number" class="field">
                    <input name="guardian_name" value="{{ $student->guardian_name }}" placeholder="Guardian name" class="field">
                    <input name="guardian_phone" value="{{ $student->guardian_phone }}" placeholder="Guardian phone" class="field">
                    <input name="address" value="{{ $student->address }}" placeholder="Address" class="field">

                    <textarea name="medical_notes" placeholder="Medical notes" class="field md:col-span-2">{{ $student->medical_notes }}</textarea>

                    <button class="btn-primary md:col-span-2">Update profile</button>
                </form>
            </section>
        </div>

        <div class="mt-6 grid gap-5 sm:gap-6 lg:grid-cols-2">
            <section class="panel">
                <div class="panel-header">
                    <p class="eyebrow">Status feed</p>
                    <h2 class="mt-1 text-lg font-bold">Application updates</h2>
                </div>

                <div class="divide-y divide-slate-200 dark:divide-slate-800">
                    @forelse($student->roomApplications->sortByDesc('created_at') as $application)
                        <div class="p-5 text-sm">
                            <div class="flex items-center justify-between gap-3">
                                <strong>{{ $application->preferredRoom?->room_number ?? 'Any room' }}</strong>
                                <span class="status-pill status-{{ $application->status }}">
                                    {{ $application->status }}
                                </span>
                            </div>
                            <p class="mt-2 text-slate-500 dark:text-slate-400">
                                {{ $application->remarks ?: $application->reason }}
                            </p>
                        </div>
                    @empty
                        <p class="p-5 text-sm text-slate-500 dark:text-slate-400">
                            No applications submitted yet.
                        </p>
                    @endforelse
                </div>
            </section>

            <section class="panel">
                <div class="panel-header">
                    <p class="eyebrow">Ledger</p>
                    <h2 class="mt-1 text-lg font-bold">Payment history</h2>
                </div>

                <div class="divide-y divide-slate-200 dark:divide-slate-800">
                    @forelse($student->payments->sortByDesc('payment_date') as $payment)
                        <div class="flex flex-wrap justify-between gap-3 p-5 text-sm">
                            <span>
                                {{ optional($payment->payment_date)->toDateString() }}
                                <span class="status-pill status-{{ $payment->status }} ml-2">
                                    {{ $payment->status }}
                                </span>
                            </span>
                            <strong>{{ number_format($payment->amount, 2) }}</strong>
                        </div>
                    @empty
                        <p class="p-5 text-sm text-slate-500 dark:text-slate-400">
                            No payment records yet.
                        </p>
                    @endforelse
                </div>
            </section>
        </div>
    @endif
</x-layout>
