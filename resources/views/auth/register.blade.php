<x-layout title="Register">
    <form method="post" action="{{ route('register') }}" class="panel w-full max-w-3xl overflow-hidden">
        @csrf
        <div class="panel-header">
            <p class="eyebrow">Student access</p>
            <h1 class="mt-1 text-2xl font-bold tracking-tight">Create your dormitory account</h1>
            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Use your student details so the admin team can review room applications quickly.</p>
        </div>
        <div class="panel-body">
            <div class="grid gap-4 md:grid-cols-2">
                <label class="text-sm font-semibold">Name <input name="name" required class="field mt-1"></label>
                <label class="text-sm font-semibold">Email <input name="email" type="email" required class="field mt-1"></label>
                <label class="text-sm font-semibold">Password <input name="password" type="password" required class="field mt-1"></label>
                <label class="text-sm font-semibold">Confirm Password <input name="password_confirmation" type="password" required class="field mt-1"></label>
                <label class="text-sm font-semibold">Student Number <input name="student_number" required class="field mt-1"></label>
                <label class="text-sm font-semibold">Course <input name="course" class="field mt-1"></label>
                <label class="text-sm font-semibold">Year Level <input name="year_level" class="field mt-1"></label>
                <label class="text-sm font-semibold">Contact Number <input name="contact_number" class="field mt-1"></label>
            </div>
            <div class="mt-6 flex flex-wrap items-center gap-3 border-t border-slate-200 pt-5 dark:border-slate-800">
                <button class="btn-primary">Register</button>
                <a class="btn-secondary" href="{{ route('login') }}">Back to login</a>
            </div>
        </div>
    </form>
</x-layout>
