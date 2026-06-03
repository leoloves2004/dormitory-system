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


                    <div class="mt-4 flex justify-between text-sm">

                        <span>
                            Status:
                            <strong>
                                {{ $request->status }}
                            </strong>
                        </span>

                        <span class="text-slate-500">
                            {{ optional($request->created_at)->diffForHumans() }}
                        </span>

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