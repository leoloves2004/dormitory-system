<x-layout title="Reports">
    <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-4">
        @foreach(['occupancy' => 'Room Occupancy', 'tenants' => 'Tenant', 'payments' => 'Payment', 'assignments' => 'Student Room Assignment'] as $type => $label)
            <section class="panel p-5">
                <p class="eyebrow">Export center</p>
                <h2 class="mt-1 font-bold">{{ $label }}</h2>
                <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Download a clean report in your preferred format.</p>
                <div class="mt-4 flex flex-wrap gap-2">
                    @foreach(['pdf','xlsx','csv','json'] as $format)
                        <a class="btn-secondary px-3 py-1.5" href="{{ route('admin.reports.export', [$type, $format]) }}">{{ strtoupper($format) }}</a>
                    @endforeach
                </div>
            </section>
        @endforeach
    </div>
    <div class="mt-5 grid gap-5 md:grid-cols-2">
        <form method="post" enctype="multipart/form-data" action="{{ route('admin.imports.students') }}" class="panel">
            @csrf
            <div class="panel-header">
                <p class="eyebrow">Bulk upload</p>
                <h2 class="mt-1 text-lg font-bold">Import students</h2>
                <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">CSV or XLSX columns: name, email, student_number, course, year_level, phone.</p>
            </div>
            <div class="panel-body space-y-4">
                <input name="file" type="file" accept=".csv,.txt,.xlsx" required class="block w-full rounded-md border border-slate-200 bg-white p-3 text-sm">
                <button class="btn-primary w-full">Upload students</button>
            </div>
        </form>
        <form method="post" enctype="multipart/form-data" action="{{ route('admin.imports.payments') }}" class="panel">
            @csrf
            <div class="panel-header">
                <p class="eyebrow">Bulk upload</p>
                <h2 class="mt-1 text-lg font-bold">Import payments</h2>
                <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">CSV or XLSX columns: student_number, amount, payment_date, due_date, method, reference_number, status, notes.</p>
            </div>
            <div class="panel-body space-y-4">
                <input name="file" type="file" accept=".csv,.txt,.xlsx" required class="block w-full rounded-md border border-slate-200 bg-white p-3 text-sm">
                <button class="btn-primary w-full">Upload payments</button>
            </div>
        </form>
    </div>
</x-layout>
