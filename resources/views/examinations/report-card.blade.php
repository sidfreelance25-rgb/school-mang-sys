@extends('layouts.app')
@section('title','Report Card')
@section('heading','Report Card')
@section('content')
<div class="panel print-area p-6">
<div class="flex justify-between items-start mb-6">
<div>
<div class="display text-2xl font-semibold">{{ \App\Models\SchoolSetting::current()->name }}</div>
<div class="text-slate-500">Student Report Card</div>
</div>
<button class="btn-brand no-print" onclick="window.print()">Print</button>
</div>
<div class="grid gap-2 sm:grid-cols-2 mb-4">
<div><strong>Student:</strong> {{ $student->user->name }}</div>
<div><strong>Admission:</strong> {{ $student->admission_no }}</div>
<div><strong>Class:</strong> {{ $student->full_class }}</div>
<div><strong>Exam:</strong> {{ $examination->name }}</div>
</div>
<table class="data"><thead><tr><th>Subject</th><th>Max</th><th>Obtained</th><th>Grade</th></tr></thead><tbody>
@foreach($results as $result)
<tr>
<td>{{ $result->examSchedule->subject->name }}</td>
<td>{{ $result->examSchedule->max_marks }}</td>
<td>{{ $result->is_absent ? 'Absent' : $result->marks_obtained }}</td>
<td>{{ $result->grade }}</td>
</tr>
@endforeach
</tbody></table>
<div class="mt-4 grid gap-2 sm:grid-cols-3">
<div class="stat-card"><div class="text-sm text-slate-500">Total</div><div class="text-xl font-bold">{{ $totalObtained }} / {{ $totalMax }}</div></div>
<div class="stat-card"><div class="text-sm text-slate-500">Percentage</div><div class="text-xl font-bold">{{ $percentage !== null ? number_format($percentage,2).'%' : 'N/A' }}</div></div>
<div class="stat-card"><div class="text-sm text-slate-500">Grade</div><div class="text-xl font-bold">{{ $grade }}</div></div>
</div>
</div>
@endsection