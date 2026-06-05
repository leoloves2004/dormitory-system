<x-layout title="Maintenance Center">

<div class="space-y-5">

    <section class="panel p-6">

        <div class="flex items-center justify-between">

            <div>

                <p class="eyebrow">
                    Maintenance Center
                </p>

                <h2 class="mt-2 text-2xl font-bold">
                    Student Maintenance Requests
                </h2>

                <p class="text-sm text-slate-500">
                    Review and monitor maintenance concerns submitted by students.
                </p>

            </div>

        </div>

    </section>


    <section class="panel">

        <div class="panel-header">

            <h3 class="text-lg font-bold">
                Incoming Requests
            </h3>

        </div>


        <div class="divide-y divide-slate-200 dark:divide-slate-800">

            @forelse($requests as $request)

                <div class="p-5">

                    <div class="flex justify-between">

                        <div>

                            <h4 class="font-bold">
                                {{ $request->title }}
                            </h4>

                            <p class="mt-1 text-xs font-semibold text-slate-500">
                                Submitted by {{ $request->user?->name ?? 'Unknown user' }}
                            </p>

                            <p class="mt-2 text-sm text-slate-500">
                                {{ $request->description }}
                            </p>

                        </div>

                        <span
                            class="
                            px-3
                            py-1
                            rounded-lg
                            text-xs
                            font-semibold
                            bg-orange-100
                            text-orange-700
                            "
                        >
                            {{ $request->priority }}
                        </span>

                    </div>


                    <div class="mt-4 flex flex-wrap items-center justify-between gap-3 text-sm">

                        <span>
                            Status:
                            <strong class="status-pill status-{{ str_replace(' ', '_', strtolower($request->status)) }} ml-2">
                                {{ $request->status }}
                            </strong>
                        </span>

                        <div class="flex items-center gap-3">
                            <span class="text-slate-500">
                                {{ optional($request->created_at)->diffForHumans() }}
                            </span>

                            @if($request->status !== 'Resolved')
                                <form method="post" action="{{ route('admin.maintenance.resolve', $request) }}">
                                    @csrf
                                    <button class="btn-primary min-h-0 px-3 py-1.5 text-xs">
                                        Mark as done
                                    </button>
                                </form>
                            @endif
                        </div>

                    </div>

                </div>

            @empty

                <div class="p-10 text-center">

                    <p class="text-slate-500">
                        No maintenance requests submitted.
                    </p>

                </div>

            @endforelse

        </div>

    </section>

</div>

</x-layout>
