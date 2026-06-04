<x-layout title="Room Applications">
    <section class="panel">
        <div class="panel-header flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="eyebrow">Review queue</p>
                <h2 class="mt-1 text-lg font-bold">Room applications</h2>
            </div>
            <form class="flex w-full flex-wrap gap-2 lg:w-auto">
                <select name="status" class="field min-w-0 flex-1 lg:w-44"><option value="">All statuses</option>@foreach(['pending','approved','rejected'] as $status)<option @selected(request('status')===$status)>{{ $status }}</option>@endforeach</select>
                <button class="btn-secondary">Filter</button>
            </form>
        </div>
        <div class="panel-body">
            <div class="table-wrap">
                <table class="data-table">
                    <thead><tr><th>Student</th><th>Preferred Room</th><th>Status</th><th>Application Date</th><th>Action</th></tr></thead>
                    <tbody>
                        @forelse($applications as $application)
                            <tr>
                                <td class="font-semibold">{{ $application->student->user->name }}</td>
                                <td>{{ $application->preferredRoom?->room_number ?? 'Any' }}</td>
                                <td><span class="status-pill status-{{ $application->status }}">{{ $application->status }}</span></td>
                                <td>{{ optional($application->application_date)->toFormattedDateString() }}</td>
                                <td>
                                    <form method="post" action="{{ route('admin.applications.update', $application) }}" class="grid w-full gap-2 xl:grid-cols-[minmax(90px,120px)_minmax(90px,120px)_minmax(0,1fr)_auto]">
                                        @csrf @method('put')
                                        <select name="room_id" class="field"><option value="">Any</option>@foreach($rooms as $room)<option value="{{ $room->id }}" @selected($application->room_id===$room->id)>{{ $room->room_number }}</option>@endforeach</select>
                                        <select name="status" class="field">@foreach(['pending','approved','rejected'] as $status)<option @selected($application->status===$status)>{{ $status }}</option>@endforeach</select>
                                        <input name="remarks" placeholder="Reviewer notes" class="field">
                                        <button class="btn-primary">Save</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-slate-500 dark:text-slate-400">No applications found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $applications->links() }}</div>
        </div>
    </section>
</x-layout>
