@extends('layouts.app')
@section('title','Attendance Report')
@section('heading','Attendance Report')
@section('content')
<form method="GET" class="panel p-4 mb-4 flex flex-wrap gap-2">
<input class="form-input w-40" type="date" name="from" value="{{ $from }}">
<input class="form-input w-40" type="date" name="to" value="{{ $to }}">
<select class="form-input w-40" name="class_id"><option value="">All classes</option>@foreach($classes as $class)<option value="{{ $class->id }}" @selected(request('class_id')==$class->id)>{{ $class->name }}</option>@endforeach</select>
<button class="btn-soft">Filter</button>
<button type="button" onclick="window.print()" class="btn-brand">Print</button>
</form>
<div class="mb-4 grid gap-3 sm:grid-cols-3">
@foreach(['present','absent','leave'] as $status)
<div class="stat-card"><div class="text-sm text-slate-500">{{ ucfirst($status) }}</div><div class="text-2xl font-bold">{{ $studentStats[$status] ?? 0 }}</div></div>
@endforeach
</div>
<div class="panel print-area"><div class="table-wrap"><table class="data"><thead><tr><th>Date</th><th>Student</th><th>Class</th><th>Status</th></tr></thead><tbody>
@foreach($records as $record)
<tr><td>{{ $record->date->format('d M Y') }}</td><td>{{ $record->student->user->name }}</td><td>{{ $record->schoolClass->name }}</td><td>{{ ucfirst($record->status) }}</td></tr>
@endforeach
</tbody></table></div><div class="p-4 no-print">{{ $records->links() }}</div></div>
@endsection