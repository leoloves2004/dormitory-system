@props(['items', 'type', 'rooms' => collect(), 'students' => collect(), 'tenants' => collect()])

@php
    $route = "admin.{$type}";
@endphp

<div class="grid min-w-0 gap-5 sm:gap-6 xl:grid-cols-[minmax(320px,400px)_minmax(0,1fr)]">
    <form method="post" action="{{ route($route.'.store') }}" class="panel">
        @csrf
        <div class="panel-header">
            <p class="eyebrow">Create record</p>
            <h2 class="mt-1 text-lg font-bold">Add {{ str($type)->singular()->headline() }}</h2>
        </div>
        <div class="panel-body grid gap-4 sm:grid-cols-2 xl:grid-cols-1">
            @if($type === 'rooms')
                <input name="room_number" placeholder="Room number" required class="field">
                <input name="room_type" placeholder="Room type" value="standard" required class="field">
                <input name="building" placeholder="Building" class="field">
                <input name="floor" type="number" placeholder="Floor" value="1" required class="field">
                <input name="capacity" type="number" placeholder="Capacity" value="4" required class="field">
                <input name="occupied_slots" type="number" placeholder="Occupied slots" value="0" class="field">
                <input name="monthly_fee" type="number" step="0.01" placeholder="Monthly fee" required class="field">
                <select name="status" class="field"><option>available</option><option>occupied</option><option>maintenance</option></select>
                <textarea name="amenities" placeholder="Amenities" class="field sm:col-span-2 xl:col-span-1"></textarea>
            @elseif($type === 'students')
                <input name="name" placeholder="Full name" required class="field">
                <input name="email" type="email" placeholder="Email" required class="field">
                <input name="password" type="password" placeholder="Temporary password" required class="field">
                <input name="student_number" placeholder="Student number" required class="field">
                <select name="room_id" class="field"><option value="">Unassigned</option>@foreach($rooms as $room)<option value="{{ $room->id }}">{{ $room->room_number }}</option>@endforeach</select>
                <input name="course" placeholder="Course" class="field"><input name="year_level" placeholder="Year level" class="field"><input name="contact_number" placeholder="Contact number" class="field">
                <select name="status" class="field"><option>active</option><option>inactive</option></select>
            @elseif($type === 'tenants')
                <select name="student_id" required class="field">@foreach($students as $student)<option value="{{ $student->id }}">{{ $student->user->name }}</option>@endforeach</select>
                <select name="room_id" required class="field">@foreach($rooms as $room)<option value="{{ $room->id }}">{{ $room->room_number }}</option>@endforeach</select>
                <input name="check_in_date" type="date" required class="field"><input name="check_out_date" type="date" class="field">
                <select name="status" class="field"><option>active</option><option>moved_out</option><option>suspended</option></select>
                <textarea name="remarks" placeholder="Remarks" class="field sm:col-span-2 xl:col-span-1"></textarea>
            @elseif($type === 'payments')
                <select name="tenant_id" required class="field">@foreach($tenants as $tenant)<option value="{{ $tenant->id }}">{{ $tenant->student?->user?->name }} - {{ $tenant->room?->room_number }}</option>@endforeach</select>
                <input name="amount" type="number" step="0.01" placeholder="Amount" required class="field">
                <input name="payment_date" type="date" required class="field"><input name="due_date" type="date" class="field">
                <input name="payment_method" placeholder="Payment method" value="cash" required class="field"><input name="reference_number" placeholder="Reference" class="field">
                <select name="status" class="field"><option>paid</option><option>pending</option><option>overdue</option><option>cancelled</option></select>
                <textarea name="notes" placeholder="Notes" class="field sm:col-span-2 xl:col-span-1"></textarea>
            @endif

            <button class="btn-primary w-full sm:col-span-2 xl:col-span-1">Save record</button>
        </div>
    </form>

    <section class="panel">
        <div class="panel-header flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="eyebrow">Directory</p>
                <h2 class="mt-1 text-lg font-bold">{{ str($type)->headline() }}</h2>
            </div>
            <form class="flex w-full flex-col gap-2 sm:flex-row lg:w-auto">
                <input name="search" value="{{ request('search') }}" placeholder="Search records" class="field min-w-0 flex-1 lg:w-64">
                <button class="btn-secondary">Search</button>
            </form>
        </div>
        <div class="panel-body">
            <div class="table-wrap">
                <table class="data-table">
                    <thead><tr>@foreach(($items->first()?->getAttributes() ?? ['records' => '']) as $key => $value)<th>{{ str($key)->headline() }}</th>@endforeach<th>Action</th></tr></thead>
                    <tbody>
                        @forelse($items as $item)
                            <tr>
                                @foreach($item->getAttributes() as $key => $value)
                                    <td>
                                        @if($key === 'status')
                                            <span class="status-pill status-{{ $value }}">{{ $value }}</span>
                                        @else
                                            {{ str($key)->contains('_date') && $value ? \Illuminate\Support\Carbon::parse($value)->toDateString() : $value }}
                                        @endif
                                    </td>
                                @endforeach
                                <td>
                                    <div class="flex w-full min-w-48 flex-col gap-2">
                                        <details class="rounded-md border border-slate-200 bg-slate-50 p-2">
                                            <summary class="cursor-pointer text-sm font-semibold text-slate-700">Edit</summary>
                                            <form method="post" action="{{ route($route.'.update', $item) }}" class="mt-3 grid gap-2">
                                                @csrf
                                                @method('put')
                                                @if($type === 'rooms')
                                                    <input name="room_number" value="{{ $item->room_number }}" required class="field">
                                                    <input name="room_type" value="{{ $item->room_type }}" required class="field">
                                                    <input name="building" value="{{ $item->building }}" class="field">
                                                    <input name="floor" type="number" value="{{ $item->floor }}" required class="field">
                                                    <input name="capacity" type="number" value="{{ $item->capacity }}" required class="field">
                                                    <input name="occupied_slots" type="number" value="{{ $item->occupied_slots }}" class="field">
                                                    <input name="monthly_fee" type="number" step="0.01" value="{{ $item->monthly_fee }}" required class="field">
                                                    <select name="status" class="field">@foreach(['available','occupied','maintenance'] as $status)<option @selected($item->status === $status)>{{ $status }}</option>@endforeach</select>
                                                    <textarea name="amenities" class="field">{{ $item->amenities }}</textarea>
                                                @elseif($type === 'students')
                                                    <input name="name" value="{{ $item->user?->name }}" required class="field">
                                                    <input name="email" type="email" value="{{ $item->user?->email }}" required class="field">
                                                    <input name="password" type="password" placeholder="Leave blank to keep password" class="field">
                                                    <input name="student_number" value="{{ $item->student_number }}" required class="field">
                                                    <select name="room_id" class="field"><option value="">Unassigned</option>@foreach($rooms as $room)<option value="{{ $room->id }}" @selected($item->room_id === $room->id)>{{ $room->room_number }}</option>@endforeach</select>
                                                    <input name="course" value="{{ $item->course }}" class="field">
                                                    <input name="year_level" value="{{ $item->year_level }}" class="field">
                                                    <input name="contact_number" value="{{ $item->contact_number }}" class="field">
                                                    <textarea name="address" class="field">{{ $item->address }}</textarea>
                                                    <select name="status" class="field">@foreach(['active','inactive'] as $status)<option @selected($item->status === $status)>{{ $status }}</option>@endforeach</select>
                                                @elseif($type === 'tenants')
                                                    <select name="student_id" required class="field">@foreach($students as $student)<option value="{{ $student->id }}" @selected($item->student_id === $student->id)>{{ $student->user->name }}</option>@endforeach</select>
                                                    <select name="room_id" required class="field">@foreach($rooms as $room)<option value="{{ $room->id }}" @selected($item->room_id === $room->id)>{{ $room->room_number }}</option>@endforeach</select>
                                                    <input name="check_in_date" type="date" value="{{ optional($item->check_in_date)->toDateString() }}" required class="field">
                                                    <input name="check_out_date" type="date" value="{{ optional($item->check_out_date)->toDateString() }}" class="field">
                                                    <select name="status" class="field">@foreach(['active','moved_out','suspended'] as $status)<option @selected($item->status === $status)>{{ $status }}</option>@endforeach</select>
                                                    <textarea name="remarks" class="field">{{ $item->remarks }}</textarea>
                                                @elseif($type === 'payments')
                                                    <select name="tenant_id" required class="field">@foreach($tenants as $tenant)<option value="{{ $tenant->id }}" @selected($item->tenant_id === $tenant->id)>{{ $tenant->student?->user?->name }} - {{ $tenant->room?->room_number }}</option>@endforeach</select>
                                                    <input name="amount" type="number" step="0.01" value="{{ $item->amount }}" required class="field">
                                                    <input name="payment_date" type="date" value="{{ optional($item->payment_date)->toDateString() }}" required class="field">
                                                    <input name="due_date" type="date" value="{{ optional($item->due_date)->toDateString() }}" class="field">
                                                    <input name="payment_method" value="{{ $item->payment_method }}" required class="field">
                                                    <input name="reference_number" value="{{ $item->reference_number }}" class="field">
                                                    <select name="status" class="field">@foreach(['paid','pending','overdue','cancelled'] as $status)<option @selected($item->status === $status)>{{ $status }}</option>@endforeach</select>
                                                    <textarea name="notes" class="field">{{ $item->notes }}</textarea>
                                                @endif
                                                <button class="btn-primary w-full">Update</button>
                                            </form>
                                        </details>
                                        <form method="post" action="{{ route($route.'.destroy', $item) }}">@csrf @method('delete')<button class="btn-danger w-full">Delete</button></form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td class="text-slate-500" colspan="20">No records found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $items->links() }}</div>
        </div>
    </section>
</div>
