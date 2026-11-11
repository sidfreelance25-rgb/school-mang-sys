@extends('layouts.app')
@section('title','Admission Report')
@section('heading','Admission Report')
@section('content')
<form method="GET" class="panel p-4 mb-4 flex flex-wrap gap-2">
<input class="form-input w-40" type="date" name="from" value="{{ request('from') }}">
<input class="form-input w-40" type="date" name="to" value="{{ request('to') }}">
<select class="form-input w-40" name="class_id"><option value="">All classes</option>@foreach($classes as $class)<option value="{{ $class->id }}" @selected(request('class_id')==$class->id)>{{ $class->name }}</option>@endforeach</select>
<button class="btn-soft">Filter</button><button type="button" class="btn-brand" onclick="window.print()">Print</button>
</form>
<div class="panel print-area"><div class="table-wrap"><table class="data"><thead><tr><th>Date</th><th>Admission</th><th>Name</th><th>Class</th><th>Session</th></tr></thead><tbody>
@foreach($students as $student)
<tr><td>{{ $student->admission_date->format('d M Y') }}</td><td>{{ $student->admission_no }}</td><td>{{ $student->user->name }}</td><td>{{ $student->full_class }}</td><td>{{ $student->academicSession->name }}</td></tr>
@endforeach
</tbody></table></div><div class="p-4 no-print">{{ $students->links() }}</div></div>
@endsection