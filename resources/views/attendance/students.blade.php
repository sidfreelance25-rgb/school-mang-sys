@extends('layouts.app')
@section('title','Student Attendance')
@section('heading','Student Attendance')
@section('content')
<form method="GET" class="panel p-4 mb-4 flex flex-wrap gap-2">
<input class="form-input w-44" type="date" name="date" value="{{ $date }}">
<select class="form-input w-40" name="class_id"><option value="">Class</option>@foreach($classes as $class)<option value="{{ $class->id }}" @selected($classId==$class->id)>{{ $class->name }}</option>@endforeach</select>
<select class="form-input w-48" name="section_id"><option value="">Section</option>@foreach($sections as $section)<option value="{{ $section->id }}" @selected($sectionId==$section->id)>{{ $section->display_name }}</option>@endforeach</select>
<button class="btn-soft">Load</button>
<a href="{{ route('attendance.report') }}" class="btn-brand">Reports</a>
</form>
@if($students->count())
<form method="POST" action="{{ route('attendance.students.store') }}" class="panel p-4">@csrf
<input type="hidden" name="date" value="{{ $date }}">
<input type="hidden" name="school_class_id" value="{{ $classId }}">
<input type="hidden" name="section_id" value="{{ $sectionId }}">
<div class="table-wrap"><table class="data"><thead><tr><th>Student</th><th>Status</th><th>Remarks</th></tr></thead><tbody>
@foreach($students as $student)
@php $row=$existing->get($student->id); @endphp
<tr>
<td>{{ $student->user->name }}<div class="text-xs text-slate-500">Roll {{ $student->roll_no }}</div></td>
<td><select class="form-input" name="attendance[{{ $student->id }}][status]">@foreach(['present','absent','leave'] as $st)<option value="{{ $st }}" @selected(($row->status ?? 'present')===$st)>{{ ucfirst($st) }}</option>@endforeach</select></td>
<td><input class="form-input" name="attendance[{{ $student->id }}][remarks]" value="{{ $row->remarks ?? '' }}"></td>
</tr>
@endforeach
</tbody></table></div>
<button class="btn-brand mt-4">Save Attendance</button>
</form>
@else
<div class="panel p-8 text-center text-slate-500">Select a class and section to mark attendance.</div>
@endif
@endsection