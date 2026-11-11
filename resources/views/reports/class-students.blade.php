@extends('layouts.app')
@section('title','Class Report')
@section('heading','Class-wise Students')
@section('content')
<form method="GET" class="panel p-4 mb-4 flex gap-2">
<select class="form-input w-48" name="class_id"><option value="">Select class</option>@foreach($classes as $class)<option value="{{ $class->id }}" @selected($classId==$class->id)>{{ $class->name }}</option>@endforeach</select>
<button class="btn-soft">Load</button><button type="button" class="btn-brand" onclick="window.print()">Print</button>
</form>
<div class="panel print-area"><div class="table-wrap"><table class="data"><thead><tr><th>Roll</th><th>Admission</th><th>Name</th><th>Section</th></tr></thead><tbody>
@foreach($students as $student)<tr><td>{{ $student->roll_no }}</td><td>{{ $student->admission_no }}</td><td>{{ $student->user->name }}</td><td>{{ $student->section->name }}</td></tr>@endforeach
</tbody></table></div></div>
@endsection