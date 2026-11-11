@extends('layouts.app')

@section('title', 'Dashboard')
@section('heading', 'Dashboard')
@section('subheading', 'Overview of school operations')

@section('content')
<div class="mb-6 rounded-2xl border border-teal-200 bg-gradient-to-br from-teal-700 to-cyan-800 p-6 text-white shadow-sm">
    <div class="text-sm uppercase tracking-[0.18em] text-teal-100">Welcome</div>
    <h2 class="display mt-2 text-3xl font-semibold">{{ auth()->user()->name }}</h2>
    <p class="mt-2 max-w-2xl text-teal-50/90">{{ $school->name }} · {{ auth()->user()->role->label() }} portal</p>
</div>

<div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
    <div class="stat-card">
        <div class="text-sm text-slate-500">Students</div>
        <div class="mt-2 text-3xl font-bold text-slate-900">{{ $stats['students'] }}</div>
    </div>
    <div class="stat-card">
        <div class="text-sm text-slate-500">Teachers & Staff</div>
        <div class="mt-2 text-3xl font-bold text-slate-900">{{ $stats['teachers'] }} / {{ $stats['staff'] }}</div>
    </div>
    <div class="stat-card">
        <div class="text-sm text-slate-500">Classes / Sections</div>
        <div class="mt-2 text-3xl font-bold text-slate-900">{{ $stats['classes'] }} / {{ $stats['sections'] }}</div>
    </div>
    <div class="stat-card">
        <div class="text-sm text-slate-500">Pending Fees</div>
        <div class="mt-2 text-3xl font-bold text-slate-900">₹{{ number_format($stats['pending_fees'], 2) }}</div>
    </div>
</div>

<div class="mt-4 grid gap-4 lg:grid-cols-3">
    <div class="panel p-5 lg:col-span-2">
        <h3 class="display text-xl font-semibold">Today's Attendance</h3>
        <div class="mt-4 grid gap-3 sm:grid-cols-2">
            <div class="rounded-xl bg-emerald-50 p-4">
                <div class="text-sm text-emerald-700">Present</div>
                <div class="text-3xl font-bold text-emerald-800">{{ $stats['today_present'] }}</div>
            </div>
            <div class="rounded-xl bg-rose-50 p-4">
                <div class="text-sm text-rose-700">Absent</div>
                <div class="text-3xl font-bold text-rose-800">{{ $stats['today_absent'] }}</div>
            </div>
        </div>

        <h3 class="display mt-6 text-xl font-semibold">Upcoming Examinations</h3>
        <div class="mt-3 space-y-2">
            @forelse($stats['upcoming_exams'] as $exam)
                <div class="flex items-center justify-between rounded-xl border border-slate-200 px-4 py-3">
                    <div>
                        <div class="font-semibold">{{ $exam->name }}</div>
                        <div class="text-sm text-slate-500">{{ $exam->start_date->format('d M Y') }} – {{ $exam->end_date->format('d M Y') }}</div>
                    </div>
                    <span class="badge badge-info">{{ $exam->status }}</span>
                </div>
            @empty
                <div class="rounded-xl border border-dashed border-slate-300 px-4 py-8 text-center text-slate-500">No upcoming examinations.</div>
            @endforelse
        </div>
    </div>

    <div class="panel p-5">
        <h3 class="display text-xl font-semibold">Announcements</h3>
        <div class="mt-3 space-y-3">
            @forelse($notices as $notice)
                <div class="rounded-xl border border-slate-200 p-3">
                    <div class="flex items-start justify-between gap-2">
                        <div class="font-semibold">{{ $notice->title }}</div>
                        @if($notice->is_important)<span class="badge badge-warning">Important</span>@endif
                    </div>
                    <p class="mt-1 text-sm text-slate-600 line-clamp-3">{{ $notice->body }}</p>
                    <div class="mt-2 text-xs text-slate-400">{{ $notice->publish_date->format('d M Y') }} · {{ ucfirst($notice->type) }}</div>
                </div>
            @empty
                <div class="text-sm text-slate-500">No announcements yet.</div>
            @endforelse
        </div>
    </div>
</div>

@if(auth()->user()->isTeacher())
    <div class="panel mt-4 p-5">
        <h3 class="display text-xl font-semibold">My Recent Homework</h3>
        <div class="table-wrap mt-3">
            <table class="data">
                <thead><tr><th>Title</th><th>Class</th><th>Due</th></tr></thead>
                <tbody>
                @foreach($roleData['homeworks'] as $hw)
                    <tr>
                        <td><a class="font-semibold text-teal-700" href="{{ route('homework.show', $hw) }}">{{ $hw->title }}</a></td>
                        <td>{{ $hw->schoolClass->name ?? '-' }}</td>
                        <td>{{ $hw->due_date->format('d M Y') }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif

@if(auth()->user()->isParent())
    <div class="panel mt-4 p-5">
        <h3 class="display text-xl font-semibold">My Children</h3>
        <div class="mt-3 grid gap-3 md:grid-cols-2">
            @foreach($roleData['children'] as $child)
                <div class="rounded-xl border border-slate-200 p-4">
                    <div class="font-semibold">{{ $child->user->name }}</div>
                    <div class="text-sm text-slate-500">{{ $child->admission_no }} · {{ $child->full_class }}</div>
                    <a href="{{ route('students.show', $child) }}" class="mt-3 inline-block text-sm font-semibold text-teal-700">View profile</a>
                </div>
            @endforeach
        </div>
    </div>
@endif

@if(auth()->user()->isStudent() && ($roleData['student'] ?? null))
    <div class="panel mt-4 p-5">
        <h3 class="display text-xl font-semibold">My Profile Snapshot</h3>
        <p class="mt-2 text-slate-600">{{ $roleData['student']->admission_no }} · {{ $roleData['student']->full_class }}</p>
        <div class="mt-4">
            <h4 class="font-semibold">Latest Homework</h4>
            <ul class="mt-2 space-y-2">
                @foreach($roleData['homeworks'] as $hw)
                    <li class="rounded-lg border border-slate-200 px-3 py-2 text-sm">{{ $hw->title }} · due {{ $hw->due_date->format('d M Y') }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

@if(auth()->user()->isAccountant())
    <div class="panel mt-4 p-5">
        <h3 class="display text-xl font-semibold">Pending Fee Invoices</h3>
        <div class="table-wrap mt-3">
            <table class="data">
                <thead><tr><th>Invoice</th><th>Student</th><th>Balance</th><th></th></tr></thead>
                <tbody>
                @foreach($roleData['unpaid_invoices'] as $invoice)
                    <tr>
                        <td>{{ $invoice->invoice_no }}</td>
                        <td>{{ $invoice->student->user->name ?? '-' }}</td>
                        <td>₹{{ number_format($invoice->balance, 2) }}</td>
                        <td><a class="text-teal-700 font-semibold" href="{{ route('fees.show', $invoice) }}">Open</a></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif
@endsection
