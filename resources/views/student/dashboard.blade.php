<x-layout title="Student Portal">

<div class="mb-5">
    <a
        href="{{ route('admin.maintenance.index') }}"
        class="btn-primary inline-block"
    >
        Maintenance Requests
    </a>
</div>

<div class="grid gap-5 lg:grid-cols-3">
    <section class="panel p-6">
        <p class="eyebrow">Current assignment</p>
        <p class="mt-3 text-4xl font-bold tracking-tight">
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
            <h2 class="mt-1 text-lg font-bold">
                Apply for a room
            </h2>
        </div>

        <form method="post" action="{{ route('student.apply') }}" class="panel-body grid gap-3 md:grid-cols-3">

            @csrf

            <select name="preferred_room_id" class="field">
                <option value="">Any available room</option>

                @foreach($rooms as $room)
                    <option value="{{ $room->id }}">
                        {{ $room->room_number }} -
                        {{ number_format($room->monthly_rate, 2) }}
                    </option>
                @endforeach
            </select>

            <input
                name="preferred_move_in_date"
                type="date"
                class="field"
            >

            <input
                name="reason"
                placeholder="Reason for request"
                required
                class="field"
            >

            <button class="btn-primary md:col-span-3">
                Submit application
            </button>

        </form>
    </section>
</div>

<div class="mt-5 grid gap-5 lg:grid-cols-[1.1fr_0.9fr]">

    <section class="panel">

        <div class="panel-header">
            <p class="eyebrow">Personal details</p>
            <h2 class="mt-1 text-lg font-bold">
                Profile
            </h2>
        </div>

        <form method="post" action="{{ route('student.profile') }}" class="panel-body grid gap-3 md:grid-cols-2">

            @csrf
            @method('put')

            <input name="course" value="{{ $student->course }}" placeholder="Course" class="field">

            <input name="year_level" value="{{ $student->year_level }}" placeholder="Year level" class="field">

            <input name="phone" value="{{ $student->phone }}" placeholder="Phone" class="field">

            <input name="guardian_name" value="{{ $student->guardian_name }}" placeholder="Guardian name" class="field">

            <input name="guardian_phone" value="{{ $student->guardian_phone }}" placeholder="Guardian phone" class="field">

            <input name="address" value="{{ $student->address }}" placeholder="Address" class="field">

            <textarea
                name="medical_notes"
                placeholder="Medical notes"
                class="field md:col-span-2"
            >{{ $student->medical_notes }}</textarea>

            <button class="btn-primary md:col-span-2">
                Update profile
            </button>

        </form>

    </section>

</div>

</x-layout>