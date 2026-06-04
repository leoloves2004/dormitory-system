<x-layout title="Visitor Logs">
    <div class="grid gap-5 lg:grid-cols-[380px_1fr]">
        <form method="post" action="{{ route('admin.visitor-logs.store') }}" class="panel">
            @csrf
            <div class="panel-header">
                <p class="eyebrow">Front desk</p>
                <h2 class="mt-1 text-lg font-bold">Log visitor</h2>
            </div>
            <div class="panel-body space-y-3">
                <input name="visitor_name" placeholder="Visitor name" required class="field">
                <input name="visitor_phone" placeholder="Phone" class="field">
                <input name="purpose" placeholder="Purpose" class="field">
                <select name="tenant_id" class="field"><option value="">Host tenant</option>@foreach($tenants as $tenant)<option value="{{ $tenant->id }}">{{ $tenant->student?->user?->name }} - {{ $tenant->room?->room_number }}</option>@endforeach</select>
                <input name="visit_date" type="date" value="{{ now()->toDateString() }}" required class="field">
                <input name="time_in" type="datetime-local" required class="field">
                <button class="btn-primary w-full">Save visitor log</button>
            </div>
        </form>
        <section class="panel">
            <div class="panel-header">
                <p class="eyebrow">Security history</p>
                <h2 class="mt-1 text-lg font-bold">Recent visitor entries</h2>
            </div>
            <div class="panel-body">
                <div class="table-wrap">
                    <table class="data-table">
                        <thead><tr><th>Visitor</th><th>Host</th><th>Room</th><th>Time In</th><th>Action</th></tr></thead>
                        <tbody>
                            @forelse($visitorLogs as $log)
                                <tr>
                                    <td class="font-semibold">{{ $log->visitor_name }}</td>
                                    <td>{{ $log->tenant?->student?->user?->name }}</td>
                                    <td>{{ $log->tenant?->room?->room_number }}</td>
                                    <td>{{ $log->time_in }}</td>
                                    <td>
                                        <div class="flex min-w-64 flex-col gap-2">
                                            <details class="rounded-md border border-slate-200 bg-slate-50 p-2 dark:border-slate-800 dark:bg-slate-950">
                                                <summary class="cursor-pointer text-sm font-semibold text-slate-700 dark:text-slate-200">Edit</summary>
                                                <form method="post" action="{{ route('admin.visitor-logs.update', $log) }}" class="mt-3 grid gap-2">
                                                    @csrf
                                                    @method('put')
                                                    <input name="visitor_name" value="{{ $log->visitor_name }}" required class="field">
                                                    <input name="visitor_phone" value="{{ $log->visitor_phone }}" class="field">
                                                    <input name="purpose" value="{{ $log->purpose }}" class="field">
                                                    <select name="tenant_id" class="field"><option value="">Host tenant</option>@foreach($tenants as $tenant)<option value="{{ $tenant->id }}" @selected($log->tenant_id === $tenant->id)>{{ $tenant->student?->user?->name }} - {{ $tenant->room?->room_number }}</option>@endforeach</select>
                                                    <input name="visit_date" type="date" value="{{ optional($log->visit_date)->toDateString() }}" required class="field">
                                                    <input name="time_in" type="datetime-local" value="{{ optional($log->time_in)->format('Y-m-d\TH:i') }}" required class="field">
                                                    <input name="time_out" type="datetime-local" value="{{ optional($log->time_out)->format('Y-m-d\TH:i') }}" class="field">
                                                    <button class="btn-primary w-full">Update</button>
                                                </form>
                                            </details>
                                            <form method="post" action="{{ route('admin.visitor-logs.destroy', $log) }}">@csrf @method('delete')<button class="btn-danger w-full">Delete</button></form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-slate-500 dark:text-slate-400">No visitor entries found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">{{ $visitorLogs->links() }}</div>
            </div>
        </section>
    </div>
</x-layout>
